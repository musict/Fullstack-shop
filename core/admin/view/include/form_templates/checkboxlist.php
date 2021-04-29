<div class="vg-element vg-full vg-left vg-box-shadow">
    <div class="vg-wrap vg-element vg-full vg-box-shadow">
        <div class="vg-element vg-full vg-left">
            <span class="vg-header ui-sortable-handle"><?=$this->translate[$row][0] ?: $row ?></span>
        </div>
        <?php if ($this->foreignData[$row]):?>
            <?php foreach ($this->foreignData[$row] as $name => $value):?>
                <?php if($value['sub']):?>
                    <div class="vg-element vg-full vg-input vg-relative vg-space-between select_wrap">
                        <span class="vg-text vg-left"><?=$value['name']?></span>
                        <span class="vg-text vg-right select_all">Выделить все</span>
                    </div>
                    <div class="option_wrap">
                        <?php foreach ($value['sub'] as $item):?>
                            <label class="custom_label" for="<?=$name?>-<?=$item['id']?>">

                                <div>
                                    <input id="<?=$name?>-<?=$item['id']?>" type="checkbox" name="<?=$row?>[<?=$name?>][<?=$item['id']?>][id]"
                                           value="<?=$item['id']?>" <?php if (isset($this->data[$row][$name][$item['id']])) echo 'checked'?>>
                                        <span class="custom_check backgr_bef"></span>
                                        <span class="label"><?=$item['name']?></span>
                                </div>

                                <?php if (!empty($item[$this->table . '_value'])):?>
                                    <input type="text" name="<?=$row?>[<?=$name?>][<?=$item['id']?>][<?=$this->table . '_value'?>]"
                                    value="<?=!empty($this->data[$row][$name][$item['id']]) ? $this->data[$row][$name][$item['id']] : ''?>"
                                    style="margin-left: 15px">
                                <?php endif;?>
                            </label>
                        <?php endforeach;?>
                    </div>
                <?php endif;?>
            <?php endforeach;?>
        <?php endif;?>
    </div>
</div>

