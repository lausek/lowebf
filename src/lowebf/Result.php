<?php

namespace lowebf;

use lowebf\Error\ConversionException;

/**
 * @template T
 * */
class Result
{
    /** @var int */
    const OK_STATE = 1;
    /** @var int */
    const ERROR_STATE = 2;

    /** @var int */
    private $state;
    /** @var T|\Throwable */
    private $argument;

    /** @param T|\Throwable $argument */
    private function __construct(int $state, $argument)
    {
        $this->state = $state;
        $this->argument = $argument;
    }

    public static function ok($value) : Result
    {
        return new Result(self::OK_STATE, $value);
    }

    public static function error(\Throwable $e) : Result
    {
        return new Result(self::ERROR_STATE, $e);
    }

    public function isOk() : bool
    {
        return $this->state === self::OK_STATE;
    }

    public function isError() : bool
    {
        return !$this->isOk();
    }

    /** 
     * try to return operations result.throws exception if result is not successful. 
     *
     * @throws \Throwable
     * */
    public function unwrap()
    {
        if ($this->isError()) {
            throw $this->argument;
        }

        return $this->argument;
    }

    /**
     * try to return operations result.throws exception if result is not successful.
     *
     * @return \Throwable
     * @throws \Exception
     * */
    public function unwrapError() : \Throwable
    {
        if ($this->isOk()) {
            throw new \Exception("called unwrapError on OK value.");
        }

        return $this->argument;
    }

    // unwrap the result or use default
    public function unwrapOr($default)
    {
        if ($this->isError()) {
            return $default;
        }

        return $this->unwrap();
    }

    // clear output buffer and set status code
    public function unwrapOrExit(Environment $env, $statusCode = null)
    {
        if ($this->isError()) {
            $statusCode = $statusCode ?? $this->getStatusCodeFromException($this->argument);

            $env->runtime()->clearOutputBuffer();

            if ($env->config()->lowebf()->isDebugEnabled()) {
                $stacktrace = $this->unwrapError();
                $env->runtime()->writeOutput("<pre>$stacktrace</pre>");
            }

            $env->runtime()->exit($statusCode);

            return;
        }

        return $this->argument;
    }

    // if the result is ok -> pass it to $mapper and wrap it in a new result.
    // returns itself otherwise.
    public function mapOk(callable $mapper) : Result
    {
        if ($this->isOk()) {
            $result = $this->unwrap();
            $result = $mapper($result);
            return Result::ok($result);
        }

        return $this;
    }

    public function mapToBool() : Result
    {
        if ($this->isOk()) {
            $result = $this->unwrap();
            return Result::ok((bool)$result);
        }

        return $this;
    }

    public function mapToInteger() : Result
    {
        if ($this->isOk()) {
            $result = $this->unwrap();

            if (is_numeric($result)) {
                return Result::ok(intval($result));
            } else {
                return Result::error(new ConversionException(gettype($result), "integer", $result));
            }
        }

        return $this;
    }

    public function mapToString() : Result
    {
        if ($this->isOk()) {
            $value = $this->unwrap();
            return Result::ok("$value");
        }

        return $this;
    }

    private function getStatusCodeFromException($e) : int
    {
        if ($e instanceof FileNotFoundException) {
            return 404;
        }

        return 500;
    }
}
