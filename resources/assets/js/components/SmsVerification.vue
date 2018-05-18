<template>
    <div class="container">
        <div class="currency-box-information sms-verification">
            <div class="setting-boder">
                <p class="title-setting">SETTING</p>
            </div>
            <div class="row" style="margin-right: 0px;">
                <accountsidebar></accountsidebar>
                <div class="col-md-9">
                    <center>
                        <div style="margin-top: 50px;width:350px;position:relative">

                            <div>
                                <input :disabled="enabled" style="margin-bottom: 5px;" class="input-signup" type="text" id="phonenumber" name="phonenumber" v-model="phoneNumber">
                                <p v-if="enabled" style="color:grey;text-align:left;margin-bottom: 0px;">You can not edit phone numbers. If you want to edit your phone number, please turn it off first</p>
                                <button @click="clickSendButton()" class="btn-primary" style="position: absolute;top: 0px;right: 0px;height: 40px;line-height: 40px;padding: 0px 10px;border:none" :disabled="sendButtonDisabled">{{ sendButtonContent }}</button>
                            </div>
                            
                            <input style="margin-top:5px;text-align:center;" class="input-signup" type="number" id="pin" name="pin" v-model="pin" placeholder="Enter the PIN you received from SMS">
                            <input type="button" v-if="!enabled" style="margin-top:10px;text-align:center" class="btn-signup" value="ENABLED SMS VERIFICATION" @click="enabledSmsVerfication()">
                            <input type="button" v-else style="margin-top:10px;background: #adadad;text-align:center" class="btn-signup" value="DISABLE SMS VERIFICATION" @click="disabledSmsVerfication()">
                        </div>
                    </center>
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
                pin: "",
                phoneNumber: "+",
                enabled : false,
                sendButtonContent: "SEND",
                sendButtonDisabled : false,
                country : "us"
            }
        },
        mounted () {
            var _this = this;
            axios.get('/api/getSmsVerification').then(function (res){
                var data = res.data;

                if(data.error) return functions.showAlert('error', data.error.message);

                _this.enabled = data.data.enabled;

                if(data.data.phoneNumber && data.data.phoneNumber.length > 1)
                    _this.phoneNumber = data.data.phoneNumber;

                if(data.data.country) {
                    _this.country = data.data.country;
                    $("#phonenumber").intlTelInput("setCountry", _this.country);
                }

            });

            $("#phonenumber").intlTelInput({
                formatOnDisplay: false,
                utilsScript: _this.$parent.SITE_URL + "/public/plugins/intl-tel-input/js/utils.js"
            });

            $("#phonenumber").intlTelInput("setCountry", _this.country);
        },
        methods: {
            countdown (count) {
                var _this = this;
                this.sendButtonDisabled = true;
                this.intervalCountdown = setInterval(function () {
                    if(count == 0) {_this.sendButtonContent = "SEND";_this.sendButtonDisabled = false; clearInterval(_this.intervalCountdown);delete _this.intervalCountdown;return}
                    _this.sendButtonContent = 'Again ('+count+'s)';
                    count--;
                },1000);
            },
            clickSendButton () {
                var _this = this;
                this.countdown(60);
                var country = $("#phonenumber").intlTelInput("getSelectedCountryData").iso2;
                axios.post('/api/sendSmsVerification', {phoneNumber: _this.phoneNumber, country: country, _token: window._token}).then(function (res) {
                    var data = res.data;

                    if(data.error) {
                        clearInterval(_this.intervalCountdown);
                        _this.sendButtonContent = "SEND";_this.sendButtonDisabled = false;delete _this.intervalCountdown;
                        return functions.showAlert('error', data.error.message);
                    }

                    if(data.success) {
                        functions.showAlert('success', data.success.message);
                    }
                });
            },
            enabledSmsVerfication () {
                var _this = this;
                functions.waitMe('.sms-verification');

                axios.post('/api/enabledSmsVerification', {pin: _this.pin, _token: window._token}).then(function (res) {
                    var data = res.data;

                    functions.hideWaitMe('.sms-verification');

                    if(data.error) {
                        return functions.showAlert('error', data.error.message);
                    }

                    if(data.success) {
                        functions.showAlert('success', data.success.message);
                        location.reload();
                    }
                });
            },
            disabledSmsVerfication () {
                var _this = this;
                functions.waitMe('.sms-verification');

                axios.post('/api/disabledSmsVerification', {pin: _this.pin, _token: window._token}).then(function (res) {
                    var data = res.data;

                    functions.hideWaitMe('.sms-verification');

                    if(data.error) {
                        return functions.showAlert('error', data.error.message);
                    }

                    if(data.success) {
                        functions.showAlert('success', data.success.message);
                        location.reload();
                    }
                });
            }
        }
    }
</script>