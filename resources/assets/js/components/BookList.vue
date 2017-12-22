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
                    <select class="form-control input-sm" name="category" id="category" v-model="category">
                        <option value="0">--全部--</option>
                        <option value="1">计算机</option>
                        <option value="2">经济学</option>
                        <option value="3">英语</option>
                    </select>
                </div>
            </div>
            <div class="form-group search_group">
                <label for="status" class="control-label no-padding-right">在架状态：</label>
                <div class="search_div">
                    <select class="form-control input-sm" name="status" id="status" v-model="status">
                        <option value="0">--全部--</option>
                        <option value="1">在架</option>
                        <option value="2">借出</option>
                        <option value="3">送人</option>
                    </select>
                </div>
            </div>
            <div class="form-group search_group">
                <label for="language" class="control-label no-padding-right">语言：</label>
                <div class="search_div">
                    <select class="form-control input-sm" name="language" id="language" v-model="language">
                        <option value="0">--全部--</option>
                        <option value="1">中文</option>
                        <option value="2">英文</option>
                    </select>
                </div>
            </div>
            <div class="form-group search_group" style="margin-top: 26px;">
                <a id="btn_search" @click="search"  href="javascript:void(0);" style="padding: 4px 12px;" class="btn btn-default">
                    <i class="fa fa-search"></i>
                    搜索
                </a>
            </div>
        </div>
        <div style="clear:both;"></div>
        <table>
            <thead>
            <tr>
                <th width="30%">书名</th>
                <th width="40%">封面</th>
                <th width="10%">类别</th>
                <!--<th width="10%">语言</th>
                <th width="10%">在架状态</th>-->
            </tr>
            </thead>
            <tbody id="booklist">
                <tr v-if="listData.length>0" v-for="item in listData">
                    <th scope="row">{{item.id}}</th>
                    <td>{{item.name}}</td>
                    <td>
                        <button v-on:click="editItem(item.id)" class="btn btn-default" >编辑</button>
                        <button v-on:click="deleteItem(item.id)" class="btn btn-default" >删除</button>
                    </td>
                </tr>
                <!--<tr id="v-for-message">
                    <td v-for="val in message">
                        {{ val }}
                    </td>
                </tr>-->
            </tbody>
            <tfoot>
            <tr>
                <td colspan=3>
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
</style>

<script>


    function getData($page,$pageSize){//获取数据，可使用各种语言替换^_^
        var $data = [];
        for (var $i=($page-1)*$pageSize+1; $i <=$page*$pageSize ; $i++) {
            $data.push( {
                id:$i,
                name:'name'+$i
            });
        }
        var $returnData = {'data':$data,'total':103};
        return $returnData;
    }
    export default{
        created(){
            //this.search();
            this.listItems();
        },
        methods:{
            search(){
                var params = {
                    'name' : this.name,
                    'category' : this.category,
                    'language' : this.language,
                    'status' : this.status
                };
                console.log(params);
                axios.post('/getbooklst', params)
                        .then(function (response) {
                            console.log(response);
                        })
                        .then(function (error) {
                            console.log(error)
                        })
            },
            listItems: function () {//列出需要的数据
                var returnData =getData(this.page,this.pageSize);
                this.listData = returnData.data;
                this.total=returnData['total'];
                this.setPageList(this.total, this.page, this.pageSize);
            },
            editItem:function ($id) {//编辑操作在这儿哟
                alert('编辑第'+$id+'条数据！');
            },
            deleteItem:function ($id) {//这里可以删除数据
                alert('删除第'+$id+'条数据！');
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
                this.listItems();
            },
            changeCurPage: function (page, pageSize) {//换页
                this.page = page;
                this.pageSize = pageSize;
                this.listItems();
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
                category: 0,
                language: 0,
                status: 0,

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
        /*data(){

            var msg = ['sdfsdfsf','dsfdffsf','ssss','66666','23wedssfdaf'];
            return{ message:msg
            }
        }*/
    }
</script>