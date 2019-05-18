<div class="callback-switcher">
    <a class="js" href="javascript:;" onclick="ModCallback.toggle(this)">Заказать обратный звонок</a>
</div>
<div class="callback-form popupblock beak">
    <form action="?sf_module_id=<?php echo $this->id ?>" method="post">
        <div class="p">
            <input type="text" class="edit" name="name" placeholder="Имя"/>
        </div>
        <div class="p">
            <input class="phoneinput edit" type="text" name="phone" placeholder="Телефон"/>
        </div>
        <div class="p pull-right">
            <button class="btn" name="callback_submit">Заказать</button>
        </div>
        <div class="c"></div>
        <div class="error"></div>
    </form>
</div>
