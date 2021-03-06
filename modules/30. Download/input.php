<fieldset class="form-horizontal">
    <legend>Downloads</legend>

	<div class="form-group">
        <label class="col-sm-2 control-label" for="headline">Überschrift</label>
        <div class="col-sm-10">
            <input class="form-control" id="headline" type="text" name="REX_INPUT_VALUE[2]" value="REX_VALUE[2]" />
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label" for="headline-level">Überschrift-Ebene</label>
        <div class="col-sm-10">
            <?php
                $options = array(
                'h2'=>'2. Ebene',
                'h3'=>'3. Ebene'
            );
            ?>
            <select name="REX_INPUT_VALUE[3]" id="headline-level" class="form-control">
            <?php foreach ($options as $k=>$v) : ?>
            <option value="<?php echo $k; ?>"<?php if ($k == "REX_VALUE[3]") echo ' selected="selected"' ?>><?php echo $v; ?></option>
            <?php endforeach ?>
            </select>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label" for="headline">Dateien zum Download</label>
        <div class="col-sm-10">
            REX_MEDIALIST[id="1" widget="1"]
        </div>
    </div>

</fieldset>
