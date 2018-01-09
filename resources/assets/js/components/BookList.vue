<template>
    <div>
        <div id="search-bar"  class="widget-body">
            <div class="form-group search_group">
                <label for="name" class="control-label no-padding-right">书名：</label>
                <div class="search_div">
                    <input type="text" class="form-control input-sm" name="name" id="name" v-model="name" size="50">
                </div>
            </div>
            <div class="form-group search_group">
                <label for="category" class="control-label no-padding-right">类别：</label>
                <div class="search_div">
                    <select class="form-control input-sm" name="category" id="category">
                    </select>
                </div>
            </div>
            <div class="form-group search_group">
                <label for="status" class="control-label no-padding-right">在架状态：</label>
                <div class="search_div">
                    <select class="form-control input-sm" name="status" id="status">
                    </select>
                </div>
            </div>
            <div class="form-group search_group">
                <label for="language" class="control-label no-padding-right">语言：</label>
                <div class="search_div">
                    <select class="form-control input-sm" name="language" id="language">
                    </select>
                </div>
            </div>
            <div class="form-group search_group" style="margin-top: 26px;">
                <a id="btn_search" @click="getData"  href="javascript:void(0);" style="padding: 4px 12px;" class="btn btn-default">
                    <i class="fa fa-search"></i>
                    搜索
                </a>
            </div>
        </div>
        <div style="clear:both;">
            <button  class="btn btn-default">新增书籍</button>
        </div>
        <table  class="table table-hover">
            <thead>
                <tr>
                    <th width="25%">书名</th>
                    <th width="35%">封面</th>
                    <th width="10%">类别</th>
                    <th width="5%">语言</th>
                    <th width="10%">在架状态</th>
                    <th width="15%">操作</th>
                </tr>
            </thead>
            <tbody id="booklist">
                <tr v-if="listData.length>0" v-for="item in listData">
                    <td scope="row">{{item.name}}</td>
                    <td>{{item.cover}}</td>
                    <td>{{dictList.book_category[item.category]}}</td>
                    <td>{{dictList.book_language[item.language]}}</td>
                    <td>{{statusList[item.status]}}</td>
                    <td>
                        <button v-on:click="editBook(item.id)" class="btn btn-default" >编辑</button>
                        <button v-if="item.status==1" v-on:click="sendBook(item.id)" class="btn btn-default">借出</button>
                        <button v-else-if="item.status==2" v-on:click="returnBook(item.id)" class="btn btn-default">收回</button>
                        <button v-on:click="deleteBook(item.id)" class="btn btn-default" >删除</button>
                    </td>
                </tr>
            </tbody>
            <tfoot>
            <tr>
                <td colspan=6>
                    <div id="pagelist">
                        <div class="row">
                            <div class="col-sm-6" style="width: 9%;margin-top: 20px;">
                                <select class="form-control input-sm" style="height: 39px;" v-model="pageData.pageSize" @change="requestAgain(pageData.pageSize)">
                                    <option value="10">10条</option>
                                    <option value="25">25条</option>
                                    <option value="50">50条</option>
                                    <option value="100">100条</option>
                                </select>
                            </div>
                            <div class="col-sm-6" style="line-height: 80px;width: 35%">
                                <div class="dataTables_info" id="sample-table-2_info">
                                    共{{pageData.total}}条，当前显示第
                                    <span v-if="pageData.pageSize==1 || pageData.total == 0 || pageData.total == 1">
                        {{pageData.itemStart}}</span>
                                    <span v-else>{{pageData.itemStart}}-{{pageData.itemEnd}}</span>
                                    条
                                </div>
                            </div>
                            <div class="col-sm-6" style="width: 56%;">
                                <div class="dataTables_paginate paging_bootstrap">
                                    <ul class="pagination">
                                        <li v-if="pageData.curPage == 1 || pageData.total == 0 || pageData.total == 1" class="prev disabled">
                                            <a>首页</a>
                                        </li>
                                        <li v-else class="prev">
                                            <a href="javascript:void(0)" rel="external nofollow"  v-on:click="changeCurPage(1,pageData.pageSize);">
                                                首页
                                            </a>
                                        </li>
                                        <li v-if="pageData.curPage == 1 || pageData.total == 0 || pageData.total == 1" class="prev disabled">
                                            <a>上一页</a>
                                        </li>
                                        <li v-else class="prev">
                                            <a href="javascript:void(0)" rel="external nofollow" v-on:click="changeCurPage(pageData.curPage-1,pageData.pageSize);">
                                                上一页</i>
                                            </a>
                                        </li>
                                        <li v-if="pageData.totalPage > 5 && pageData.curPage > 3" class="next disabled">
                                            <a>...</a>
                                        </li>
                                        <template v-for="pageItem in pageData.pageIndex">
                                            <li v-if="pageData.curPage == pageItem" class="active">
                                                <a>{{pageItem}}</a>
                                            </li>
                                            <li v-else>
                                                <a href="javascript:void(0)" rel="external nofollow"  v-on:click="changeCurPage(pageItem,pageData.pageSize);">
                                                    {{pageItem}}
                                                </a>
                                            </li>
                                        </template>
                                        <li v-if="pageData.totalPage > 5 && pageData.curPage < pageData.totalPage-2" class="next disabled">
                                            <a>...</a>
                                        </li>
                                        <li v-if="pageData.curPage == pageData.totalPage || pageData.total == 0 || pageData.total == 1" class="next disabled">
                                            <a>下一页</i></a>
                                        </li>
                                        <li v-else class="next">
                                            <a href="javascript:void(0)" rel="external nofollow" v-on:click="changeCurPage(pageData.curPage+1,pageData.pageSize);">
                                                下一页</i>
                                            </a>
                                        </li>
                                        <li v-if="pageData.curPage == pageData.totalPage || pageData.total == 0 || pageData.total == 1" class="next disabled">
                                            <a>末页</a>
                                        </li>
                                        <li v-else class="next">
                                            <a href="javascript:void(0)" rel="external nofollow" v-on:click="changeCurPage(pageData.totalPage,pageData.pageSize);">
                                                末页
                                            </a>
                                        </li>
                                        <template v-if="pageData.totalPage > 5" class="next disabled">
                                            <li>
                                                <input value="" ref="goPage" class="input-mini" type="text" style="height: 32px;width:40px;margin:auto 5px auto 20px;line-height: 24px;">
                                                <label><a href="javascript:void(0)" rel="external nofollow" v-on:click="goPage(pageData.pageSize,pageData.totalPage)">Go</a></label>
                                            </li>
                                        </template>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
            </tfoot>
        </table>
    </div>
</template>
<style type="text/css">
    .search_group{float:left;margin-right: 25px;}
    .btn{padding: 2px 4px;margin: 1px 2px;}
</style>

<script>
    export default{
        created(){
            this.getDicts();
            this.getData();
        },
        methods:{
            getDicts: function () {
                axios.post('/getDictList', {setkey_like: 'book_'})
                        .then((response) => {
                            var dicts = {};
                            var lanstr = '<option value="0">--全部--</option>';
                            var catstr = '<option value="0">--全部--</option>';
                            for(var i in response.data) {
                                if (!dicts[response.data[i]['set_key']]) {
                                    dicts[response.data[i]['set_key']] = [];
                                }
                                if (response.data[i]['set_key'] == 'book_language') {
                                    lanstr += '<option value="' + response.data[i]['id'] + '">' + response.data[i]['set_value'] + '</option>';
                                }
                                if (response.data[i]['set_key'] == 'book_category') {
                                    catstr += '<option value="' + response.data[i]['id'] + '">' + response.data[i]['set_value'] + '</option>';
                                }
                                dicts[response.data[i]['set_key']][response.data[i]['id']] = response.data[i]['set_value'];
                            }
                            if (dicts) {
                                for (var j in dicts) {
                                    dicts[j][-1] = '其它';
                                }
                                lanstr += '<option value="-1">其它</option>';
                                catstr += '<option value="-1">其它</option>';
                            }
                            this.dictList = dicts;
                            console.log(this.dictList);
                            $("#language").html(lanstr);
                            $("#category").html(catstr);
                            // status 类型
                            var statusstr = '<option value="0">--全部--</option>';
                            for (var i in this.statusList) {
                                statusstr += '<option value="' + i + '">' + this.statusList[i] + '</option>';
                            }
                            $("#status").html(statusstr);
                        }).catch (function (error) {
                            console.log(error);
                        })
            },
            getData: function () {
                var params = {
                    'name' : this.name,
                    'category' : $("#category").val(),
                    'language' : $("#language").val(),
                    'status' : $("#status").val(),
                    'pageNo' : this.page,
                    'pageSize' : this.pageSize
                };
                axios.post('/getbooklst', params)
                        .then((response) => {
                            this.listData = response.data.data;
                            console.log(this.listData);
                            this.total = response.data.total;
                            this.setPageList(this.total, this.page, this.pageSize);
                        })
                        .catch(function (error) {
                            console.log(error)
                        })
            },
            editBook:function ($id) {
                alert('编辑第'+$id+'条数据！');
            },
            deleteBook:function ($id) {
                alert('删除第'+$id+'条数据！');
            },
            sendBook:function ($id) {
                alert('借出第'+$id+'条数据！');
            },
            returnBook:function ($id) {
                alert('收回第'+$id+'条数据！');
            },
            setPageList: function (total, page, pageSize) {
                total = parseInt(total);
                var curPage = parseInt(page);
                pageSize = parseInt(pageSize);
                var totalPage = Math.ceil(total / pageSize);
                var itemStart = (curPage - 1) * pageSize + 1;
                if (curPage == totalPage) {
                   var  itemEnd = total;
                } else {
                    itemEnd = curPage * pageSize;
                }
                var pageIndex = [];
                if (curPage >= 1 && curPage <= totalPage) {
                    if (totalPage < 5) {//5页以内
                        for (var $i = 1; $i <= totalPage; $i++) {
                            pageIndex.push($i);
                        }
                    } else {//大于5页
                        if (curPage == 1) {
                            pageIndex = [curPage, curPage + 1, curPage + 2, curPage + 3, curPage + 4];
                        } else if (curPage == 2) {
                            pageIndex = [curPage - 1, curPage, curPage + 1, curPage + 2, curPage + 3];
                        } else if (curPage == totalPage - 1) {
                            pageIndex = [curPage - 3, curPage - 2, curPage - 1, curPage, totalPage];
                        } else if (curPage == totalPage) {
                            pageIndex = [curPage - 4, curPage - 3, curPage - 2, curPage - 1, curPage];
                        } else {
                            pageIndex = [curPage - 2, curPage - 1, curPage, curPage + 1, curPage + 2];
                        }
                    }
                }

                this.pageData.curPage = curPage;
                this.pageData.pageSize = pageSize;
                this.pageData.total = total;
                this.pageData.totalPage = totalPage;
                this.pageData.pageIndex = pageIndex;
                this.pageData.itemStart = itemStart;
                this.pageData.itemEnd = itemEnd;
            },
            requestAgain: function (pageSize) {
                this.page = 1;
                this.pageSize = pageSize;
                this.getData();
            },
            changeCurPage: function (page, pageSize) {//换页
                this.page = page;
                this.pageSize = pageSize;
                this.getData();
            },
            goPage: function (pageSize, totalPage) {//跳转页
                var pageIndex = this.$refs.goPage.value;
                if (pageIndex <= 0) {
                    pageIndex = 1;
                    this.$refs.goPage.value = 1;
                } else if (pageIndex >= totalPage) {
                    pageIndex = totalPage;
                    this.$refs.goPage.value = totalPage;
                }
                this.changeCurPage(pageIndex, pageSize);
            }
        },
        data () {
            return {
                name: '',
                statusList:{1:'在架', 2:'借出', 3:'送人'},
                dictList:[],
                listData:[],
                page: 1,//当前页码
                pageSize: 10,//每页条数
                total:0,//总数
                pageData: {
                    curPage: 1,
                    pageSize: 10,
                    total: 0,
                    totalPage: 0,
                    pageIndex: [],
                    itemStart: 0,
                    itemEnd: 0
                }
            }
        },
    }
</script>