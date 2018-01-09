<template>
    <div class="container">
        <div class="page-body">
            <div class="row">
                <div class="col-xs-12 col-md-12">
                    <div class="widget">
                        <div class="widget-header ">
                            <label for="category" class="control-label no-padding-right">系统字典设置</label>
                            <div class="widget-buttons">
                                <a href="#" data-toggle="maximize">
                                    <i class="fa fa-expand"></i>
                                </a>
                                <a href="#" data-toggle="collapse">
                                    <i class="fa fa-minus"></i>
                                </a>
                                <a href="#" data-toggle="dispose">
                                    <i class="fa fa-times"></i>
                                </a>
                            </div>
                        </div>
                        <div class="widget-body">
                            <div class="table-toolbar">
                                <div class="form-group">
                                    <select id="setkeyList" @change="changeSetkey" class="form-control" style="display: inline-block; width: 180px;">
                                        <option value="book_category">书籍类别</option>
                                        <option value="book_language">书籍语言</option>
                                    </select>
                                    <a id="addNewDict" @click="showAddNewDictDiv" href="javascript:void(0);" class="btn btn-default">
                                        <i class="fa fa-plus"></i>
                                        增加字典数据
                                    </a>
                                </div>
                            </div>
                            <table  class="table table-hover">
                                <thead>
                                <tr>
                                    <th width="70%">字典数据值</th>
                                    <th width="30%">操作</th>
                                </tr>
                                </thead>
                                <tbody id="dictlist">
                                <tr v-if="listData.length>0" v-for="item in listData">
                                    <td scope="row">{{item.set_value}}</td>
                                    <td>
                                        <button v-on:click="editDict(item)" class="btn btn-default" >编辑</button>
                                        <button v-on:click="deleteDict(item)" class="btn btn-default" >删除</button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="addNewDictDiv" style="background-color:#e3e3e3; position:absolute; z-index:99; left:0; top:0; display:none; width:100%; height:1000px;opacity:0.8;filter: alpha(opacity=50);-moz-opacity: 0.5;">
            <div class="modal-dialog" style="width: 400px;">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" @click="hideAddNewDictDiv">
                            &times;
                        </button>
                        <h4 class="modal-title" id="modalTitle">新增数据字典</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <form id="frmInputData">
                                    <div class="form-group">
                                        <input type="hidden" id="dict_id">
                                        <input type="hidden" id="set_key">
                                        <label for="txt_set_key">数据类型</label>
                                        <input type="text" class="form-control" readonly="readonly" id="txt_set_key" >
                                    </div>
                                    <div class="form-group">
                                        <label for="txt_set_value">字典数据<sup class="required">*</sup></label>
                                        <input type="text" class="form-control" id="txt_set_value"  placeholder="请输入">
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div style="display: none; color: red;" id="saveTip"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="btn_save" @click="saveDict">
                            <i class='fa fa-save'></i>保存
                        </button>
                        <button type="button" class="btn btn-danger" @click="hideAddNewDictDiv">
                            <i class='fa fa-times'></i>关闭
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data () {
            return {
                listData:[],
            }
        },
        created(){
            this.getDictList('book_category');
        },
        methods: {
            getDictList: function (set_key) {
                var params = {
                    'set_key' : set_key,
                };
                axios.post('/getDictList', params)
                        .then((response) => {
                    this.listData = response.data;
                }).catch(function (error) {
                    console.log(error)
                })
            },
            changeSetkey: function () {
                this.getDictList($('#setkeyList').val());
            },
            showAddNewDictDiv: function () {
                $("#addNewDictDiv").css('display', 'block');
                $("#modalTitle").text("新增字典数据");
                this.change(-1);
            },
            hideAddNewDictDiv: function () {
                $("#addNewDictDiv").css('display', 'none');
            },
            change: function (id, value) {
                $('#dict_id').val(id);
                if (id != -1) {
                    $('#txt_set_value').val(value);
                } else {
                    $('#txt_set_value').val('');
                }
                $('#set_key').val($('#setkeyList').val());
                $('#txt_set_key').val($('#setkeyList option:selected').text());
            },
            saveDict: function() {
                var params = {
                    'id' : $('#dict_id').val(),
                    'set_value' : $('#txt_set_value').val(),
                    'set_key' : $('#set_key').val(),
                };
                axios.post('/saveDict', params)
                        .then((response) => {
                        if (response.data.code == 1) {
                            this.getDictList($('#set_key').val());
                            $("#saveTip").css('display', 'none');
                            $("#addNewDictDiv").css('display', 'none');
                        } else {
                            $("#saveTip").text(response.data.msg);
                            $("#saveTip").css('display', 'block');
                        }
                }).catch(function (error) {
                    console.log(error)
                })
            },
            editDict: function (item) {
                this.change(item.id,item.set_value);
                $("#addNewDictDiv").css('display', 'block');
                $("#modalTitle").text("编辑字典数据");
            },
            deleteDict: function (item) {
                axios.post('/deleteDict', {id:item.id, set_key:item.set_key})
                        .then((response) => {
                            if (response.data.code == 1) {
                                this.getDictList($('#setkeyList').val());
                            } else {
                                alert(response.data.msg);
                            }
                        }).catch(function (error) {
                            console.log(error)
                        })
            },
        }
    }
</script>
