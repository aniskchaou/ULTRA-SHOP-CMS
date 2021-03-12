<?php

class SNP_NHP_Options_zoho_fields extends SNP_NHP_Options
{
    public function __construct($field = array(), $value ='', $parent)
    {

        parent::__construct($parent->sections, $parent->args, $parent->extra_tabs);

        $this->field = $field;
        $this->value = $value;
    }

    public function render()
    {

        $class = (isset($this->field['class'])) ? $this->field['class'] : '';

        if (!$this->value) {
            $this->value = $this->field['std'];
        }

        $this->field['options'] = snp_ml_get_zoho_fields();

        $class = (isset($this->field['class'])) ? 'class="' . $this->field['class'] . '" ' : '';

        echo '<div id="' . $this->field['id'] . '" '.$class.'>';
        if ($this->field['options']) {
            echo '<table>';
            echo '<thead>';
            echo '<tr>';
            echo '<th>Section</th>';
            echo '<th>Label</th>';
            echo '<th>Options</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            foreach ($this->field['options'] as $record) {
                echo '<tr>';
                echo '<td>'.$record->section.'</td>';
                echo '<td>'.$record->label.'</td>';
                if (is_array($record->options)) {
                    echo '<td>';
                    foreach($record->options as $value) {
                        echo '-'.$value .'<br>';
                    }
                    echo '</td>';
                }
                echo '</tr>';
            }
            echo '</tbody>';
        } else {
            echo 'Error encountered during fields listing';
        }

        echo (isset($this->field['desc']) && !empty($this->field['desc'])) ? ' <span class="description">' . $this->field['desc'] . '</span>' : '';
        echo '</div>';
    }

    public function enqueue() {}
}
