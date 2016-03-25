<?php

use yii\helpers\Html;
use yii\helpers\Url;
use backend\widgets\common\LinkPages;

$this->title = '角色&权限列表';
$this->params['breadcrumbs'][] = $this-> title;


?>

<div class="wrapper wrapper-content animated fadeInRight">

    <div class="ibox-content">
        <div class="row">
            <div class="col-sm-1 m-b-xs">
                <?=Html::a("创建 <i class='icon-plus'></i>" , ['create' ], ['class' => 'btn btn-sm btn-primary'])?>
            </div>
            <!--<div class="col-md-1 m-b-xs">
                <select class="input-sm form-control input-s-sm inline">
                    <option value="1">角色</option>
                    <option value="2">权限</option>
                </select>
            </div>-->
            <!--<div class="col-sm-3">
                <div class="input-group">
                    <input type="text" placeholder="请输入关键词" class="input-sm form-control"> <span class="input-group-btn">
                                        <button type="button" class="btn btn-sm btn-primary"> 搜索</button> </span>
                </div>
            </div>-->
        </div>
        <hr>
        <div class="table-responsive">


            <table class ="table table-striped table_base">
                <thead >
                <tr >
                    <!--                 <th> -->
                    <!--                     <div class="checker"><span><input type="checkbox" value="1" name="select_all" class="select-on-check-all"></span></div> -->
                    <!--                 </th> -->
                    <th >ID</th >
                    <th >名称 </th >
                    <th >类型 </th >
                    <th >描述 </th >
                    <th >创建时间 </th >
                    <th >操作 </th >
                </tr >
                </thead >
                <tbody >
                <?php if ( empty($data[ 'data'])): ?>
                    <tr ><td colspan ="20"><?=Yii::t('common' ,'Not find data') ?> </td ></tr >
                <?php else: ?>
                    <?php $i = $data['start'];?>
                    <?php foreach ( $data['data'] as $list):?>
                        <tr data-key =" <?=$list[ 'name'] ?>" >
                            <!--                 <td> -->
                            <!--                     <div class="checker"><span><input type="checkbox" value="158" name="select"></span></div> -->
                            <!--                 </td> -->
                            <td ><?= $i++?> </td >
                            <td ><?= Html::encode($list[ 'name']) ?></ td>
                            <td ><?= Html::encode(($list[ 'type']==1)? '角色': '权限') ?></ td>
                            <td ><?= Html::encode($list[ 'description']) ?></ td>
                            <td> <?=Html:: encode($list['created_at']?date('Y-m-d',$list['created_at']):'未设置') ?></ td>
                            <td >
                                <a href= "<?= Url::to([ 'update', 'id'=>$list[ 'name']]); ?>" >编辑 </a >
                                <a href= "<?= Url::toRoute([ 'permission/set', 'id'=>$list[ 'name']]); ?>" >权限 </a >
                                <a class ="del" href= "javaScript:;">删除</a >
                            </td >
                        </tr >
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody >
            </table >
            <?=Yii::t('common' , '{start}-{end} a total of {total}',['start' => $data[ 'start'], 'end'=> $data['end'],'total'=> $data['count' ]])?> </div >
        <?=LinkPages:: widget(['pagination' => $pages]);?>
        <input type ="hidden" name="delUrl" value= "<?= Url::to([ 'delete']) ?>" >
    </div>
</div >

<script>
    (function($) {
        var delUrl        = $('input[name=delUrl]').val(),
            delAllUrl     = $('input[name=delAllUrl]').val(),
            obj_thead     = $('.table_base > thead'),
            obj_tbody     = $('.table_base > tbody'),
            obj_check     = $('.table_base .checker'),
            obj_check_ipt = obj_tbody.find('input[name=select]'),
            obj_check_all = obj_thead.find('input[name=select_all]'),
            submitData    = {};
        submitData[$('[name=csrf-param]').attr('content')] = $('[name=csrf-token]').attr('content');

        /*复选框start*/
        obj_check.hover(function(){
            $(this).addClass('hover');
        },function(){
            $(this).removeClass('hover');
        });

        obj_check.click(function(){
            var _checkbox = $(this).find('input[name^=select]'),
                isChecked = _checkbox[0].checked;
            _checkbox.parent()[ isChecked ? 'addClass' : 'removeClass' ]('checked');
            if(_checkbox.hasClass('select-on-check-all')){
                $.each(obj_check_ipt,function(){
                    $(this)[0].checked = isChecked ? true : false;
                    $(this).parent()[ isChecked ? 'addClass' : 'removeClass' ]('checked');
                })
            }
            ifAllCheck();
            checkToIpt();
        });

        $('.bottomBtn > #selectall').click(function(){
            obj_check_all.click();
        });

        $('.bottomBtn > #export').click(function(){
            if(confirm('您确定要删除选中项吗？')){
                var ids = $('.bottomBtn > #selected').val();
                if(ids){
                    doAjax(
                        delAllUrl,
                        {ids: ids},
                        function(json){
                            if(json.status){
                                location.reload(true);
                            }else{
                                alert(json.msg);
                            }
                        }
                    );
                }
            }
        });

        function ifAllCheck(){
            var allCheck  = true
            $.each(obj_check_ipt,function(){
                if( !$(this)[0].checked ){
                    allCheck = false;
                    return;
                }
            })
            obj_check_all[0].checked = allCheck;
            obj_check_all.parent()[ allCheck ? 'addClass' : 'removeClass' ]('checked');
        }

        function checkToIpt(){
            var selectedTemp = [];
            $.each(obj_check_ipt,function(){
                if( $(this)[0].checked ){
                    selectedTemp.push( $(this).parents('tr:first').attr('data-key') );
                }
            });
            $('.bottomBtn > #selected').val( selectedTemp.join(',') );
        }
        /*复选框end*/

        $('.table_base .del').click(function(){ // 点击删除
            if(confirm('您确定要删除此项吗？')){
                submitData['id'] = $(this).parents('tr:first').attr('data-key');
                doAjax(
                    delUrl,
                    submitData,
                    function(json){
                        if(json.status){
                            location.reload(true);
                        }else{
                            alert(json.msg);
                        }
                    }
                );
            }
        });

        function renderTmp(item, tmp) { // 渲染值
            return tmp.replace( /\{.+?\}/g, function($1) { return item[$1.slice(1, -1)] ? item[$1.slice(1, -1)] : ''; });
        }

        function trBC(){ // tr背景颜色间隔
            obj_tbody.find('td').css('background-color','#ffffff');
            obj_tbody.find('tr:visible').each(function(key,val){
                if(key%2 == 0){
                    $(val).find('td').css('background-color','#f9f9f9');
                }
            });
        }

        function doAjax(url,data,sucFn){
            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'json',
                data: data,
                success: sucFn,
                error: function(){
                    alert('交互错误');
                }
            });
        }

    })(jQuery)
</script>