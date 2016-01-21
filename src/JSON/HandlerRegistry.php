<?php

namespace DC\JSON;

class HandlerRegistry {
    /**
     * @var \DC\JSON\Handler[]
     */
    private $handlers = [];

    /**
     * @param \DC\JSON\Handler[] $handlers
     */
    public function __construct(array $handlers = null)
    {
        if ($handlers == null) {
            $handlers = [
                new Handlers\DateTimeHandler()
            ];
        }

        foreach ($handlers as $handler) {
            foreach ($handler->getHandledTypes() as $type) {
                $this->handlers[$type] = $handler;
            }
        }
    }

    /**
     * @param $type
     * @return Handler
     * @throws Exceptions\MultipleHandlersFoundException
     */
    function findHandler($type) {
        if (array_key_exists($type, $this->handlers)) {
            return $this->handlers[$type];
        }

        $specificHandlers = [];
        foreach ($this->handlers as $handlerType => $handler) {
            // if the handler implements a more specific type than the one we are looking for, use it
            if (is_subclass_of($handlerType, $type) && !in_array($handler, $specificHandlers, true)) {
                $specificHandlers[] = $handler;
            }
        }

        if (count($specificHandlers) == 1) {
            $foundHandler = reset($specificHandlers);
            $this->handlers[$type] = $foundHandler;
            return $foundHandler;
        }
        else if (count($specificHandlers) > 1) {
            throw new Exceptions\MultipleHandlersFoundException($type, array_map('get_class', $specificHandlers));
        }

        $this->handlers[$type] = null;
        return null;
    }
}