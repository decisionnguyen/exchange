<template>
    <div class="container">
        <div class="currency-box-information account-infomation">
            <div class="setting-boder">
                <p class="title-setting">SETTING</p>
            </div>
            <div class="row" style="margin-right: 0px;">
                <accountsidebar></accountsidebar>
                <div class="col-md-9 boder-left-information">
                    <div class="row">
                        <div v-if="!emailConfimed" style="padding: 5px 10px;background: #3498db;color: white;font-weight: 600;" >You need to verify your email to increase your withdrawal limit to 2 BTC <a class="btn btn-sm" style="font-size: 10px;padding: 5px 15px;margin-top:0px;width:auto;vertical-align: 1px;" @click="sendEmailConfirmAgain()">Send email confirm again</a></div>
                        <div v-if="emailConfimed && !kycVerification" style="padding: 5px 10px;background: #3498db;color: white;font-weight: 600;" >You need to verify your personal (KYC Verification) to increase your withdrawal limit to 50 BTC<a class="btn btn-sm" style="font-size: 10px;padding: 5px 15px;margin-top:0px;width:auto;vertical-align: 1px;" :href="$parent.SITE_URL + '/user/kycVerification'">KYC Verification</a></div>
                        <div class="col-md-6" style="padding-right: 0px;">
                            <div :class="emailConfimed == true ? 'middle background-verified' : 'middle background-unverified'" style="text-align:center;height:130px;margin-left:-5px;color:white;display: flex;align-items: center;justify-content: center;">
                                <div>
                                    <p style="color:white" class="text-info">{{ user.email }}<span v-if="!emailConfimed" @click="sendEmailConfirmAgain()" class="unverified">Unverified</span> </p>
                                    <!-- <p style="color:white" class="text-email">{{ user.username }}</p> -->
                                    <p style="color:white" class="text-email">Withdrawal Limit: <b>{{ withdrawalLimit }}</b> BTC</p>
                                </div>
                                <div class="mark"><i :class="emailConfimed == true ? 'fas fa-check-circle' : 'fas fa-times-circle'"></i></div>
                            </div>
                        </div>
                        <div class="col-md-6 enable-authen">
                            <i style="font-size: 27px;vertical-align: 4px;margin-left: 20px;" class="fas fa-key"></i>
                            <div style="display: inline-block;margin-left: 20px;">
                                <p class="font-bold-authen">Two-factory Authentication</p>
                                <p style="line-height:10px;">Will be used during login and withdrawal</p>
                            </div>
                            <div class="material-switch pull-right" style="margin-top: 50px;margin-right: 20px;" @click="redirect($parent.SITE_URL + '/user/twoAuth')">
                                <input id="tow-factory" name="tow-factory" type="checkbox" v-if="twoAuth" :checked="twoAuth"/>
                                <label for="tow-factory" class="label-success"></label>
                            </div>
                            <!-- <div style="float: right;margin-top: 6px;">
                                <a class="btn btn-sm" v-bind:href="this.$parent.SITE_URL + '/user/twoAuth' " style="background:#97c248;width:auto;border-radius:0px;padding:10px;margin-left:5px;border:none" v-if="!twoAuth">Enable</a>
                                <a class="btn btn-sm" v-bind:href="this.$parent.SITE_URL + '/user/twoAuth' " v-if="twoAuth" style="width:auto;border-radius:0px;background:grey;padding:10px;margin-left:5px;border:none">Disable</a>
                            </div> -->
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 enable-authen" style="border-right: 1px solid #cccccc;">
                            <i style="font-size: 27px;vertical-align: 4px;margin-left: 20px;" class="fas fa-comment-alt"></i>
                            <div style="display: inline-block;margin-left: 20px;">
                                <p class="font-bold-authen">SMS Verification</p>
                                <p style="line-height:10px;">Used to verify a withdrawal order</p>
                            </div>
                            <div class="material-switch pull-right" style="margin-top: 50px;margin-right: 20px;" @click="redirect($parent.SITE_URL + '/user/smsVerification')">
                                <input id="sms-verify" name="sms-verify" type="checkbox" v-if="smsVerification" :checked="smsVerification"/>
                                <label for="sms-verify" class="label-success"></label>
                            </div>
                        </div>
                        <div class="col-md-6 enable-authen email-verification" style="border-top: 1px solid #cccccc;">
                            <i style="font-size: 27px;vertical-align: 4px;margin-left: 20px;" class="fas fa-envelope"></i>
                            <div style="display: inline-block;margin-left: 20px;">
                                <p class="font-bold-authen">Email Verification</p>
                                <p style="line-height:10px;">Used to verify a withdrawal order</p>
                            </div>
                            <div class="material-switch pull-right" style="margin-top: 50px;margin-right: 20px;">
                                <input id="email-verify" name="email-verify" type="checkbox" v-if="emailVerification" :checked="emailVerification"/>
                                <label @click="switchEmailVerification()" for="email-verify" class="label-success"></label>
                            </div>
                        </div>
                    </div>
                    <div style="clear: both;"></div>
                    <div class="row" style="margin-right:0px;">
                        <div class="table-lastlogin">
                            <div class="table-left currency-table trading table-responsive">
                                <table class="table">
                                    <thead class="thead-light">
                                        <tr>
                                            <th scope="col">DATE</th>
                                            <th scope="col">IP</th>
                                            <th scope="col">Region</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="log in lastLogin">
                                            <td>{{ log.created_at }}</td>
                                            <td>{{ log.ip }}</td>
                                            <td>{{ log.region }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- modal -->
        <div class="modal wallet" id="disabled-email-verification" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;top:10%">
            <div class="modal-dialog modal-sm">
                <div class="modal-header" style="background: #adadad;">
                    <h2 style="margin-bottom: 0px;color:white;text-align:left;font-size: 14px;">DISABLED EMAIL VERIFICATION</h2>
                    <button type="button" class="close-modal-button" data-dismiss="modal" style="top: 9px;"><i style="font-size: 25px;" class="zmdi zmdi-close"></i></button>
                </div>
                <div class="loginmodal-container" style="position:relative">
                    <!-- SMS Verification -->
                    <div style="position:relative">
                        <p style="color: grey;text-align: left;margin-bottom: 20px;">We just sent you a security code into your email. Please enter the code here to disable this function</p>
                        <div class="single-input input-group">
                            <span style="border-radius:0px;border-right: none;background:white;" class="input-group-addon"><i class="zmdi zmdi-key"></i> </span>
                            <input class="input-6-digit" maxlength="6" type="text" v-model="emailVerificationPin" name="emailVerificationPin" style="border-left:none;margin:0px;text-align:center;font-weight:700;padding-left:44px;font-size:20px;color:black;">
                        </div>
                    </div>

                    <button style="width: 100%;background: #adadad;margin-top: 20px;" class="btn-signup" @click="disabledEmailVerificaiton()">DISABLED</button>
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
                user : {
                    email : "",
                    username : ""
                },
                withdrawalLimit: 0,
                emailConfimed :false,
                kycVerification: false,
                twoAuth : false,
                smsVerification : false,
                emailVerification: false,
                lastLogin : [],
                emailVerificationPin: ""
            }
        },
        mounted () {
            var _this = this;
            axios.get('/api/getUserInfo').then(function (res){
                var data = res.data;

                if(data.error) return functions.showAlert('error', data.error.message);

                _this.user = data.data.user;
                _this.lastLogin = data.data.lastLogin;
                _this.emailConfimed = data.data.email.confirmed;
                _this.twoAuth = data.data.twoAuth;
                _this.smsVerification = data.data.smsVerification;
                _this.emailVerification = data.data.emailVerification;
                _this.kycVerification = data.data.kycVerification;

                if(_this.emailConfimed) _this.withdrawalLimit = 2;
                if(_this.emailConfimed && _this.kycVerification) _this.withdrawalLimit = 50;
            });

            $(".account-infomation .unverified").hover(function () {
                $(this).html("Send again");
                $(this).css('background', 'rgb(151, 194, 72)');
                $(this).css('cursor', 'pointer');
            }, function () {
                $(this).html("Unverified");
                $(this).css('background', 'grey');
                $(this).css('cursor', 'none');
            });

        },
        methods: {
            redirect : function (url) {
                location.href = url;
            },
            switchEmailVerification : function () {

                var _this = this;

                if(_this.emailVerification) {

                    $("#disabled-email-verification").modal('show');

                    axios.get('/api/sendEmailVerification').then(function (res){
                        var data = res.data;
                        if(data.error) return functions.showAlert('error', data.error.message);
                    });
                    
                    return;
                }
                
                axios.get('/api/switchEmailVerification').then(function (res){
                    var data = res.data;

                    if(data.error) return functions.showAlert('error', data.error.message);

                    _this.emailVerification = !_this.emailVerification;

                    return functions.showAlert('success', data.success.message);

                });
            },
            disabledEmailVerificaiton : function () {
                var _this = this;
                axios.get('/api/switchEmailVerification/' + _this.emailVerificationPin).then(function (res){
                    var data = res.data;

                    if(data.error) return functions.showAlert('error', data.error.message);

                    location.reload();

                    return functions.showAlert('success', data.success.message);

                });
            },
            sendEmailConfirmAgain : function () {
                functions.waitMe('.middle');
                axios.get('/api/resendEmailConfirm/').then(function (res){
                    var data = res.data;
                    functions.waitMe('.middle');

                    if(data.error) return functions.showAlert('error', data.error.message);

                    location.reload();

                    return functions.showAlert('success', data.success.message);

                });
            }
        }
    }
</script>