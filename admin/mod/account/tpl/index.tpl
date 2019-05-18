<?php AdminPlugAlert::output() ?>

<div class="row">
    <div class="col-md-6 ">

        <div class="portlet ">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-reorder"></i> Данные пользователя
                </div>
            </div>
            <div class="portlet-body form">
                <form method="post" class="form-horizontal" action="">
                    <div class="form-body">
                        <div class="form-group">
                            <label class="col-md-3 control-label">Роль</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" readonly="" value="<?php echo $data['role_name'] ?>" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label">Логин</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" readonly="" value="<?php echo $data['login'] ?>" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label">Имя</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" name="name" value="<?php echo $data['name'] ?>" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label">Email</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" name="email" value="<?php echo $data['email'] ?>" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label">Задать новый пароль</label>
                            <div class="col-md-9">
                                <input type="password" class="form-control" name="password" value="" />
                                <span class="help-block">Оставьте пустым, если менять не нужно.</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-offset-3 col-md-9">
                                <button class="btn btn-primary" type="submit">Применить</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
<div class="clear"></div>