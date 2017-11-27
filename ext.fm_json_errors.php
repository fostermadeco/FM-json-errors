<?php

class Fm_json_errors_ext
{
    /**
     * Hooks to install
     *
     * @var array
     */
    protected $hooks = [
        'output_show_message',
    ];

    /**
     * Extension version number
     *
     * @var string
     */
    public $version = '1.0.0';

    /**
     * Activates the extension
     */
    public function activate_extension()
    {
        foreach ($this->hooks as $hook) {
            ee()->db->insert('extensions', [
                'class' => __CLASS__,
                'method' => $hook,
                'hook' => $hook,
                'settings' => '',
                'priority' => 10,
                'version' => $this->version,
                'enabled' => 'y',
            ]);
        }
    }

    /**
     * Grab the error messages from the EE error page to return via JSON
     *
     * @param $data
     * @param $output
     * @return mixed
     */
    public function output_show_message($data, $output)
    {
        if (ee()->input->is_ajax_request() === false) {
            return $output;
        }

        set_status_header('500', 'Error');

        $xml = new DOMDocument();
        $xml->loadHTML($output);

        $errors = [];
        foreach($xml->getElementsByTagName('li') as $li) {
            $errors[] = $li->nodeValue;
        }

        echo json_encode(['errors' => $errors]);
        exit;
    }
}