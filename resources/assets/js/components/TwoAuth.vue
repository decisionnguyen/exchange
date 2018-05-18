<template>
    <div class="container">
        <div class="currency-box-information">
            <div class="setting-boder">
                <p class="title-setting">SETTING</p>
            </div>
            <div class="row">
                <accountsidebar></accountsidebar>
                <div class="col-md-9 no-padding">
    
                    <div v-if="enabled == 0">

                        <div class="step-1" v-if="step == 1">
                            <div class="noti-authen" >
                                <p class="red" style="margin-botom:5.1px !important;"><b>Attention!</b></p>
                                <p>If you enabled the Google Authenticator for your account, you will not be able to login without Google Authenticator code.</br>
                                    Please, read the manual for Google Authenticator carefully and learn how to recover it in case of loss.</br>
                                    Your account will be inaccessible for 10 days after reset!</p>
                            </div>
                            <p class="text-qr">Secret code:</p>
                            <p class="text-qr-s">{{ secret }}</p>
                            <img style="margin-bottom:14px;margin-left:14px;" v-if="qrCode != '' " v-bind:src="qrCode" width="180" height="180">
                            <div style="clear: both;"></div>
                            <button class="qr-continue" @click="step = 2">Continue</button>
                        </div>

                        <div class="col-md-9" v-if="step == 2">
                            <p class="text-confirm">Enter one-time password from the Google Authenticator app.</p>
                            <input type="text" v-model="pin" placeholder="6 Digit Pin Authenticator" name="confirm" class="input-confirm">
                            <div style="clear: both;"></div>
                            <button class="qr-back" @click="step = 1">BACK</button>
                            <button type="submit" @click="enabledTwoAuth()" class="qr-submit">ENABLED</button>
                        </div>

                    </div>

                    <div class="col-md-9" v-if="enabled == 1">
                        <p class="text-confirm">Enter one-time password to disabled Two-factory Authentication.</p>
                        <input type="text" v-model="pin" placeholder="6 Digit Pin Authenticator" name="confirm" class="input-confirm">
                        <div style="clear: both;"></div>
                        <button type="submit" @click="disabledTwoAuth()" class="qr-back">DISABLED</button>
                    </div>


                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Vue2Filters from 'vue2-filters'
    import functions from './../functions.js';

    export default {
        data () {
            return {
                enabled : 0,
                secret : "",
                qrCode : "",
                step : 1,
                pin : ''
            }
        },
        mounted () {
            this.initFunction();
        },
        methods: {
            initFunction : function () {
                var _this = this;
                axios.get('/api/getTwoAuth/').then(function (res){
                    var data = res.data;
                    if(data.error) return functions.showAlert('error', data.error.message);
                    _this.enabled = data.data.enabled;
                    _this.secret = data.data.secret;
                    _this.qrCode = data.data.qrcode;
                });
            },
            setSessionUserTwoAuth : function (flag,callback) {
                var _this = this;
                axios.get('/api/setSessionUserTwoAuth/' + flag).then(function (res){
                    var data = res.data;
                    if(data.error) return functions.showAlert('error', data.error.message);
                    callback();
                });
            },
            enabledTwoAuth : function () {
                var _this = this;
                axios.get('/api/enabledTwoAuth/' + this.pin).then(function (res){
                    var data = res.data;
                    if(data.error) return functions.showAlert('error', data.error.message);
                    functions.showAlert('success', 'Enabled successful');
                    _this.enabled = 1;
                    _this.setSessionUserTwoAuth(0, function () {
                        window.location = _this.$parent.SITE_URL + '/user/loginTwoAuth/user&twoAuth';
                    });
                });
            },
            disabledTwoAuth : function () {
                var _this = this;
                axios.get('/api/disabledTwoAuth/' + this.pin).then(function (res){
                    var data = res.data;
                    if(data.error) return functions.showAlert('error', data.error.message);
                    functions.showAlert('success', 'Disabled successful');
                    _this.initFunction();
                });
            }
        }
    }
</script>