<form id="<?php echo $this->name; ?>-form" class="plug-form" method="<?php echo $this->method; ?>"  action="<?php echo $this->action; ?>">
    <table>
        <?php foreach ($this->fields as $field) : ?>
            <tr>
                <td><label class="plug-form-label"><?php echo $field->label, $field->v_requied ? ' <span>*</span>' : ''; ?></label></td>
                <td>
                    <?php
                    $field->html();
                    echo '<div class="plug-form-error">';
                    if (isset($this->errors[$field->name])) {
                        foreach ($this->errors[$field->name] as $err) {
                            echo '— &nbsp;', $err, '<br />';
                        }
                    }
                    echo '</div>';
                    if ($field->comment) {
                        echo '<div class="plug-form-comment">', $field->comment, '</div>';
                    }
                    ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <hr />
    <div class="buttons">
        <button class="btn" name="<?php echo $this->name ?>[submit]"><?php echo $this->btn_submit; ?></button>
    </div>
</form>