<?php
namespace Core\Engine;

class Console {
    private string $action;
    private array $options = [];

    public function __construct(array $input)
    {
        $this->parseCoomand($input);
    }

    /**
     * @return array
     */
    private function parseCoomand(array $_argv)
    {
        $this->action = $_argv[1];
        $this->options = array_slice($_argv, 2, sizeof($_argv));
    }

    public function handleCommands()
    {
        $this->command($this->action, $this->options);
    }

    private function command(string $action, array $options)
    {
        switch ($action) {
            case 'serve':
                exec('php -S localhost:'.((isset($options[0]) && $options[0]) ? $options[0] : '5000'));

                break;
            
            default:
                # code...
                break;
        }
    }
}