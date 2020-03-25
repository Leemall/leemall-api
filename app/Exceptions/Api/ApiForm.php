<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020-03-19
 * Time: 下午 4:34
 */

namespace App\Exceptions\Api;

use Closure;
use Illuminate\Support\Arr;
use App\Exceptions\Api\Form\HasHooks;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Symfony\Component\HttpFoundation\Response;

class ApiForm
{
    use HasHooks;
    protected static $initCallbacks;
    /**
     * Eloquent model of the form.
     *
     * @var Model
     */
    protected $model;

    /**
     * @var \Illuminate\Validation\Validator
     */
    protected $validator;

    /**
     * @var Builder
     */
    protected $builder;
    /**
     * Data for save to current model from input.
     *
     * @var array
     */
    protected $updates = [];
    /**
     * Input data.
     *
     * @var array
     */
    protected $inputs = [];
    /**
     * Ignored saving fields.
     *
     * @var array
     */
    protected $ignored = [];
    /**
     * only saving fields.
     *
     * @var array
     */
    protected $fields = [];
    /**
     * @var bool
     */
    protected $isSoftDeletes = false;

    public function __construct($model, Closure $callback = null)
    {
        $this->model = $model;

        if ($callback instanceof Closure) {
            $callback($this);
        }

        $this->isSoftDeletes = in_array(SoftDeletes::class, $this->classUsesDeep($this->model), true);

        $this->callInitCallbacks();
    }
    protected function classUsesDeep($class, $autoload = true)
    {
        $traits = [];

        do {
            $traits = array_merge(class_uses($class, $autoload), $traits);
        } while ($class = get_parent_class($class));

        foreach ($traits as $trait => $same) {
            $traits = array_merge(class_uses($trait, $autoload), $traits);
        }

        return array_unique($traits);
    }
    /**
     * Call the initialization closure array in sequence.
     */
    protected function callInitCallbacks()
    {
        if (empty(static::$initCallbacks)) {
            return;
        }

        foreach (static::$initCallbacks as $callback) {
            $callback($this);
        }
    }

    /**
     * @return Model
     */
    public function model(): Model
    {
        return $this->model;
    }

    public function store()
    {
        $data = \request()->all();

        if (($response = $this->prepare($data)) instanceof Response) {
            return $response;
        }
        $inserts = $this->updates;
        foreach ($inserts as $column => $value) {
            $this->model->setAttribute($column, $value);
        }

        $this->model->save();
        if (($result = $this->callSaved()) instanceof Response) {
            return $result;
        }
        return \response()->json();
    }
    /**
     * Handle update.
     *
     * @param int  $id
     * @param null $data
     *
     * @return bool|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|mixed|null|Response
     */
    public function update($id, $data = null)
    {
        $data = ($data) ?: request()->all();

        $this->model = $this->model->findOrFail($id);

        if (($response = $this->prepare($data)) instanceof Response) {
            return $response;
        }

        DB::transaction(function () {
            $updates = $this->updates;
            foreach ($updates as $column => $value) {
                $this->model->setAttribute($column, $value);
            }
            $this->model->save();
        });

        if (($result = $this->callSaved()) instanceof Response) {
            return $result;
        }
        return \response()->json();
    }
    /**
     * Prepare input data for insert or update.
     *
     * @param array $data
     *
     * @return mixed
     */
    protected function prepare($data = [])
    {
        if (($response = $this->callSubmitted()) instanceof Response) {
            return $response;
        }
        //
        $data = $this->onlyFields($data);

        $this->inputs = array_merge($this->removeIgnoredFields($data), $this->inputs);

        if (($response = $this->callSaving()) instanceof Response) {
            return $response;
        }

        $this->updates = $this->inputs;
    }

    /**
     * Remove ignored fields from input.
     *
     * @param array $input
     *
     * @return array
     */
    protected function removeIgnoredFields($input): array
    {
        Arr::forget($input, $this->ignored);

        return $input;
    }

    protected function onlyFields($input): array
    {
        if($this->fields){
            $input = Arr::only($input, $this->fields);
        }
        return $input;
    }

    /**
     * only fields to save.
     *
     * @param string|array $fields
     *
     * @return $this
     */
    public function only($fields): self
    {
        $this->fields = array_merge($this->fields, (array)$fields);

        return $this;
    }

    /**
     * Ignore fields to save.
     *
     * @param string|array $fields
     *
     * @return $this
     */
    public function ignore($fields): self
    {
        $this->ignored = array_merge($this->ignored, (array)$fields);

        return $this;
    }

    /**
     * Getter.
     *
     * @param string $name
     *
     * @return array|mixed
     */
    public function __get($name)
    {
        return $this->input($name);
    }
    /**
     * Setter.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return array
     */
    public function __set($name, $value)
    {
        return Arr::set($this->inputs, $name, $value);
    }
    /**
     * Get or set input data.
     *
     * @param string $key
     * @param null   $value
     *
     * @return array|mixed
     */
    public function input($key, $value = null)
    {
        if ($value === null) {
            return Arr::get($this->inputs, $key);
        }

        return Arr::set($this->inputs, $key, $value);
    }

}