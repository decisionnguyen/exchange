<template>
    <div class="container">
        <div class="currency-box-information">
            <div class="setting-boder">
                <p class="title-setting">SETTING</p>
            </div>
            <div class="row">
                <accountsidebar></accountsidebar>
                <div class="col-md-9" style="padding-left: 0px;">
                    <div class="noti-authen"><p class="red"><b>Attention!</b></p> <p>Please protect your account with security features. This will avoid any risk of hacker attacks on your account, we will not be responsible for the case of account theft because you do not setting security.</p></div>
                    <ul class="list-group" style="width: 60%;margin-top: 50px;margin-left:auto;margin-right:auto">
                        <li class="list-group-item">
                            Enabled Two-factory Authentication for withdraw
                            <div class="material-switch pull-right">
                                <input id="tow-factory" name="tow-factory" type="checkbox"/>
                                <label for="tow-factory" class="label-success"></label>
                            </div>
                        </li>
                        <li class="list-group-item">
                            Enabled SMS Authentication for withdraw
                            <div class="material-switch pull-right">
                                <input id="sms" name="sms" type="checkbox"/>
                                <label for="sms" class="label-success"></label>
                            </div>
                        </li>
                        <li class="list-group-item">
                            Enabled Email Authentication for withdraw
                            <div class="material-switch pull-right">
                                <input id="email" name="email" type="checkbox"/>
                                <label for="email" class="label-success"></label>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Vue2Filters from 'vue2-filters'
    import functions from './../functions.js'

    export default {
        data () {
            return {
                
            }
        },
        mounted () {
            var _this = this;
            axios.get('/api/getUserInfo').then(function (res){
                var data = res.data;

                if(data.error) return functions.showAlert('error', data.error.message);

            });
        },
        methods: {
            
        }
    }
</script>