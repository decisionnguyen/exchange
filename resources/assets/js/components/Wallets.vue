<template>
    <!--Currency Table are start-->
    <div class="currency-table-area wallet">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="currency-table table-responsive" data-mcs-theme="dark" style="max-height:500px;overflow:auto">
                        <div id="custom-search-input" class="search-custom" style="margin-bottom:0px;"><div class="input-group col-md-12" style="position: relative"><i style="position: absolute;top: 5px;left: 11px;color: grey;font-size: 22px; z-index: 3;" class="zmdi zmdi-search"></i><input v-model="searchText" style="padding-left: 34px;" type="text" placeholder="Search wallet..." class="form-control input-sm"></div></div>
                        <table class="table">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col">
                                        <div class="checkbox" style="padding-left:19px;margin:0px;min-height: 16px;">
                                            <input id="hide-zero-balance" type="checkbox" style="width: auto;height: auto" value="true" v-model="hideZeroBalance" @click="hideZero()">
                                            <label for="hide-zero-balance">
                                                Hide Zero Balance
                                            </label>
                                        </div>
                                    </th>
                                    <th style="text-align:center" scope="col">Deposit</th>
                                    <th style="text-align:center" scope="col">withraw</th>
                                    <th scope="col border">available</th>
                                    <th scope="col">on pending</th>
                                    <th scope="col">on orders</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(coin,key) in filterByAndOrderBy()" :class="!coin.wallet || parseFloat(coin.wallet.amount) == 0 ? 'zero-balance' : '' ">
                                    <td class="wallet-name">{{ coin.symbol }} <span style="font-size:80%;color:grey"><img style="float:left;height:15px;margin-right:5px;" :src="$parent.SITE_URL + '/public/img/coins/' + coin.logo">{{ coin.name }}</span></td>
                                    <td v-if="coin.wallet" style="text-align:center" @click="showDeposit(coin, coin.wallet)">
                                        <a href="#" data-toggle="modal" data-target="#deposit"><img src="/public/img/icon/color-icon.png" alt=""></a>
                                    </td>
                                    <td v-else style="text-align:center" @click="showDeposit(coin)">
                                        <a href="#" data-toggle="modal" data-target="#deposit"><img src="/public/img/icon/color-icon.png" alt=""></a>
                                    </td>
                                    <td v-if="coin.wallet" style="text-align:center" @click="showWithdraw(coin, coin.fee, coin.wallet)">
                                        <a href="#" data-toggle="modal" data-target="#withdraw"><img style="max-width:17px;" src="/public/img/icon/color-r-icon.png" alt=""></a>
                                    </td>
                                    <td v-else style="text-align:center" @click="showWithdraw(coin, coin.fee)">
                                        <a href="#" data-toggle="modal" data-target="#withdraw"><img style="max-width:17px;" src="/public/img/icon/color-r-icon.png" alt=""></a>
                                    </td>
                                    
                                    <td class="number" style="font-size:14px;" v-if="coin.wallet == null" v-html="formatFloatNumber(0) + ' ' + coin.symbol"></td>
                                    <td class="number" style="font-size:14px;" v-if="coin.wallet != null" v-html="formatFloatNumber(coin.wallet.amount) + ' ' + coin.symbol"></td>
                                    <td class="number" :class="coin.onPending != 0 ? 'on-pending' : ''" v-html="formatFloatNumber(coin.onPending) + ' ' + coin.symbol"></td>
                                    <td class="number" v-html="formatFloatNumber(coin.onOrder) + ' ' + coin.symbol"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <p v-if="deposit.pending && deposit.pending.length > 0" class="title-table-history" style="margin-top:10px;">DEPOSIT PENDING</p>
                    <div v-if="deposit.pending && deposit.pending.length > 0" class="currency-table table-responsive" style="max-height: 500px;overflow: auto">
                        <table class="table">
                            <thead class="thead-grey">
                                <tr class="history-fonts">
                                    <th scope="col">Date</th>
                                    <th scope="col border">Units</th>
                                    <th scope="col">Status</th>
                                </tr>
                            </thead>
                            <tbody v-for="data in deposit.pending">
                                <tr  class="padding-row">
                                    <td>{{ data.created_at }}</td>
                                    <td class="number" v-html="formatFloatNumber(data.amount) + ' ' + data.coinSymbol"></td>
                                    <td style="color:#f39c12">pending ({{data.confirmations}}) </td>
                                    <td><button class="view-history" @click="showTransactionDetail('deposit-pending', data.id, $event)">show</button></td>
                                </tr>
                                <tr :class="'transaction-info ' + 'deposit-pending-' + data.id">
                                    <td colspan="4">
                                    <i class="zmdi zmdi-pin"></i> Address : {{ data.address }}<br>
                                    <i class="zmdi zmdi-link"></i> Txid : {{ data.txid }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <p v-if="deposit.history && deposit.history.length > 0" class="title-table-history" style="margin-top:10px;">DEPOSIT HISTORY</p>
                    <div v-if="deposit.history && deposit.history.length > 0" class="currency-table table-responsive custom-scroll-bar2" style="max-height: 500px;overflow: auto">
                        <table class="table">
                            <thead class="thead-grey">
                                <tr class="history-fonts">
                                    <th scope="col">Date</th>
                                    <th scope="col border">units</th>
                                    <th scope="col">status</th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody v-for="data in deposit.history">
                                <tr  class="padding-row">
                                    <td>{{ data.created_at }}</td>
                                    <td class="number" v-html="formatFloatNumber(data.amount) + ' ' + data.coinSymbol"></td>
                                    <td class="green">Complete</td>
                                    <td><button class="view-history" @click="showTransactionDetail('deposit-history', data.id, $event)">show</button></td>
                                </tr>
                                <tr :class="'transaction-info ' + 'deposit-history-' + data.id">
                                    <td colspan="4">
                                    <i class="zmdi zmdi-pin"></i> Address : {{ data.address }}<br>
                                    <i class="zmdi zmdi-link"></i> Txid : {{ data.txid }}
                                    </td>
                                </tr>
                            </tbody>    
                        </table>
                    </div>
                </div>
                <div class="col-md-6">
                    <p v-if="withdraw.pending && withdraw.pending.length > 0" class="title-table-history" style="margin-top:10px;">WITHDRAW PENDING</p>
                    <div v-if="withdraw.pending && withdraw.pending.length > 0" class="currency-table table-responsive" style="max-height: 500px;overflow: auto">
                        <table class="table">
                            <thead class="thead-grey">
                                <tr class="history-fonts">
                                    <th scope="col">Date</th>
                                    <th scope="col border">units</th>
                                    <th scope="col">status</th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="data in withdraw.pending" class="padding-row">
                                    <td>{{ data.created_at }}</td>
                                    <td class="number" v-html="formatFloatNumber(data.total) + ' ' + data.coinSymbol"></td>
                                    <td style="color:#f39c12">pending</td>
                                    <td style="color:#f39c12"><button @click="showCacelWithdrawModal(data.id)" type="button" style="width:30px;height:15px;margin-top:-3px;" class="btn btn-danger custom-danger-icon" data-toggle="modal" data-target="#cancelWithdraw"><i style="font-size:14px;" class="zmdi zmdi-close"></i></button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <p v-if="withdraw.history && withdraw.history.length > 0" class="title-table-history" style="margin-top:10px;">WITHDRAW HISTORY</p>
                    <div v-if="withdraw.history && withdraw.history.length > 0" class="currency-table table-responsive" style="max-height: 500px;overflow: auto">
                        <table class="table">
                            <thead class="thead-grey">
                                <tr class="history-fonts">
                                    <th scope="col">Date</th>
                                    <th scope="col border">units</th>
                                    <th scope="col">status</th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody v-for="data in withdraw.history">
                                <tr  class="padding-row">
                                    <td>{{ data.created_at }}</td>
                                    <td class="number" v-html="formatFloatNumber(data.amount) + ' ' + data.coinSymbol"></td>
                                    <td v-if="data.status == 1" class="green">complete</td>
                                    <td v-if="data.status == 0" class="red">Cancelled</td>
                                    <td><button class="view-history" @click="showTransactionDetail('withdraw-history', data.id, $event)">show</button></td>
                                </tr>
                                <tr :class="'transaction-info ' + 'withdraw-history-' + data.id">
                                    <td colspan="4">
                                    <i class="zmdi zmdi-pin"></i> Address : {{ data.address }}<br>
                                    <i class="zmdi zmdi-link"></i> Txid : {{ data.txid }}
                                    </td>
                                </tr>
                            </tbody> 
                        </table>
                    </div>
                </div>
            </div>
        </div>

         <!--    Deposit modal area start    -->
        <div class="modal wallet" id="deposit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;top:10%">
            <div class="modal-dialog">
                <div class="modal-header">
                    <h2 style="margin-bottom: 0px;color:white;text-align:left;"><i class="zmdi zmdi-plus-circle-o"></i> DEPOSIT {{ depositModal.coin.name }} ({{ depositModal.coin.symbol }})</h2>
                    <button type="button" class="close-modal-button" data-dismiss="modal"><i class="zmdi zmdi-close"></i></button>
                </div>
                <div class="loginmodal-container">
                    <img v-if="depositModal.wallet && depositModal.wallet.qrcode != null" :src="$parent.SITE_URL + '/public/qrcode/' + depositModal.wallet.qrcode" :alt="depositModal.wallet.qrcode" class="qr-code">

                    <h4 class="green" v-if="depositModal.wallet">Wallet address:</h4>
                    <form style="position:relative;width:100%;" v-if="depositModal.wallet.address">
                        <input  type="text" v-model="depositModal.wallet.address" name="address" id="address">
                        <button @click="copyAddress" style="background:#97C248;border:none;position:absolute;top:0px;right:0px;border-radius:0px;margin-top:0px;height:43px;width:100px;" class="btn color-btn copy-button" type="button">Copy</button>
                    </form>

                    <div class="button" v-if="!depositModal.wallet">
                        <button type="button" class="color-btn" @click="createNewAddress">Create new address</button>
                    </div>

                    <!-- <div class="login-help">
                        <p><span class="green">Please note:</span> That a deposit fee of 0.0006 BTC is to be charged</p>
                    </div> -->

                </div>
            </div>
        </div>
        <!--    Deposit modal area end    -->
        <!--    Withdraw modal area start    -->
        <div class="modal wallet withdraw" id="withdraw" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;top:10%">
            <div class="modal-dialog">
                <div class="modal-header">
                    <h2 style="margin-bottom: 0px;color:white;text-align:left;"><i class="zmdi zmdi-minus-circle-outline"></i> WITHDRAW {{ withdrawModal.coin.name }} ({{withdrawModal.coin.symbol}})</h2>
                    <button type="button" class="close-modal-button" data-dismiss="modal"><i class="zmdi zmdi-close"></i></button>
                </div>
                <div class="loginmodal-container" style="position:relative">
                    <form :action="$parent.SITE_URL + '/wallet/postWithdraw' " method="POST">
                        <input type="hidden" name="_token" :value="withdrawModal._token">
                        <input type="hidden" name="coin_id" :value="withdrawModal.coin.id">
                        <div class="single-input input-group" style="margin-bottom: 30px;">
                            <span style="border-radius:0px;border-right: none;background:white;" class="input-group-addon"><i class="zmdi zmdi-pin"></i> Address</span>
                            <input type="text" name="address" style="margin-bottom: 0px;border-left:none">
                        </div>
                        <div class="single-input input-group">
                            <span style="border-radius:0px;border-right: none;background:white;" class="input-group-addon">Amount</span>
                            <input type="text" name="amount" v-model="withdrawModal.wallet.amount" @blur="calcFee()" style="border-left:none;margin:0px;text-align:center;font-weight:400;padding-left:44px;font-size:16px;">
                            <span style="border-radius:0px;" class="input-group-addon">{{ withdrawModal.coin.symbol }}</span>
                        </div>
                        <div class="single-input">
                            <p style="float:right;font-size:12px;color:rgb(217, 217, 217);margin:0px;">Fee: {{ withdrawModal.fee }} {{ withdrawModal.coin.symbol }}</p>
                            <p @click="withdrawAll(cutFloatNumber(withdrawModal.wallet.amount))" class="hover-bold" style="float:left;font-size:12px;color:grey;margin:0px;cursor:pointer">Balance: {{ cutFloatNumber(withdrawModal.wallet.amount) }} {{ withdrawModal.coin.symbol }}</p>
                        </div>
                        <div style="clear:both;margin-bottom:10px;"></div>
                        <div class="single-input input-group" style="margin-bottom:30px;">
                            <span style="border-radius:0px;border-right: none;background:rgb(217, 217, 217)" class="input-group-addon">You'll receive</span>
                            <input type="text" v-model="withdrawModal.receive" disabled="disabled" style="background:#d9d9d9;margin-bottom:0px;text-align:center;font-weight:400;padding-left:44px;;font-size:16px;border-left:0px;">
                            <span style="border-radius:0px;" class="input-group-addon">{{ withdrawModal.coin.symbol }}</span>
                        </div>
                        <p class="note-modal" style="margin-bottom:20px;text-align:left;font-size:12px;">+ Please check the exact address and the amount you want to withdraw. We'll not be responsible when your withdrawal order is completed<br>+ Do not withdraw directly to a crowdfund or ICO. We will not credit your account with tokens from that sale.</p>

                        <!-- Two Auth -->
                        <div v-if="isEnabledTwoAuth" class="single-input input-group" style="margin-bottom:20px;">
                            <span style="border-radius:0px;border-right: none;background:white;" class="input-group-addon"><i class="zmdi zmdi-key"></i> Two-factory Authentication</span>
                            <input class="input-6-digit" maxlength="6" type="text" name="twoAuthPin" style="border-left:none;margin:0px;text-align:center;font-weight:700;padding-left:44px;font-size:20px;">
                        </div>
                        <!-- SMS Verification -->
                        <div v-if="smsVerification != false" style="position:relative">
                            <div class="single-input input-group" style="margin-bottom:20px;">
                                <span style="border-radius:0px;border-right: none;background:white;" class="input-group-addon"><i class="zmdi zmdi-comment-alt"></i> SMS Verification</span>
                                <input class="input-6-digit" maxlength="6" type="text" name="smsVerificationPin" style="border-left:none;margin:0px;text-align:center;font-weight:700;padding-left:44px;font-size:20px;">
                            </div>

                            <input type="button" @click="clickSendSmsButton()" class="btn-primary" style="opacity:1;color:white;position: absolute;top: 0px;right: 0px;height: 43px;line-height: 40px;padding: 0px 10px;border:none;width:110px;" value="SEND SMS">
                        </div>
                        <!-- Email Verification -->
                        <div v-if="emailVerification != false" style="position:relative">

                            <div class="single-input input-group">
                                <span style="border-radius:0px;border-right: none;background:white;" class="input-group-addon"><i class="zmdi zmdi-email"></i> EMAIL Verification</span>
                                <input class="input-6-digit" maxlength="6" type="text" name="emailVerificationPin" style="border-left:none;margin:0px;text-align:center;font-weight:700;padding-left:44px;font-size:20px;">
                            </div>

                            <input type="button" @click="clickSendEmailButton()" class="btn-primary" style="opacity:1;color:white;position: absolute;top: 0px;right: 0px;height: 43px;line-height: 40px;padding: 0px 10px;border:none;width:110px;" value="SEND EMAIL">
                        </div>

                        <button type="submit" class="submit-withdraw">Withdraw</button>
                    </form>
                    <img :src="$parent.SITE_URL + '/public/img/modal-footer-bg.png'" style="    width: 20%;position: absolute;bottom: 0px;right: 0px;">
                </div>
            </div>
        </div>
        <!--    Withdraw modal area end    -->
        <!--    Confirm cacel modal    -->
        <div class="modal wallet confirm" id="cancelWithdraw" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;top:30%">
            <div class="modal-dialog modal-sm">
                <div class="loginmodal-container" style="padding:0px;">
                    <button type="button" class="close-modal-button" data-dismiss="modal"><i class="zmdi zmdi-close"></i></button>

                    <h2 style="margin-top:18px;">Are you sure?</h2>
                    <p style="padding:15px;">Are you sure you want to cancel your withdrawal?<br></p>
                    <form :action="$parent.SITE_URL + '/wallet/cancelWithdraw' " method="POST">
                        <input type="hidden" name="_token" v-model="cancelWithdrawModal._token">
                        <input type="hidden" name="id" v-model="cancelWithdrawModal.id">
                        <button style="width:50%;float:left;border:none;" type="button" class="btn-danger" data-dismiss="modal">NO</button>
                        <button style="width:50%;float:right;border:none;" type="submit" class="submit-withdraw btn-success">YES</button>
                    </form>
                        
                </div>
            </div>
        </div>
        <!--    Confirm cacel modal    -->
    </div>
    <!--Currency Table are end-->
</template>

<script>

    import functions from './../functions.js';
    import Vue2Filters from 'vue2-filters';

    export default {
        data () {
            return {
                searchText: "",
                coins : [],
                deposit : [],
                withdraw: [],
                isEnabledTwoAuth: false,
                smsVerification: false,
                emailVerification: false,
                isNotCreated : true,
                depositModal : {
                    coin : {},
                    wallet : false
                },
                withdrawModal : {
                    coin : {},
                    wallet : false,
                    fee : 0,
                    receive: "",
                    _token : ""
                },
                cancelWithdrawModal : {
                    _token : "",
                    id : null
                },
                hideZeroBalance: false
            }
        },
        mounted () {
            var _this = this;
            axios.get('/api/getWallet').then(function (res){

                var data = res.data;

                if(data.error) return functions.showAlert('error', data.error.message);

                _this.coins = data.data.coins;
                _this.deposit = data.data.deposit;
                _this.withdraw = data.data.withdraw;
                _this.isEnabledTwoAuth = data.data.isEnabledTwoAuth;
                _this.smsVerification = data.data.smsVerification;
                _this.emailVerification = data.data.emailVerification;

            });
        },
        methods: {
            filterByAndOrderBy : function () {
                return this.orderBy(this.filterBy(this.coins, this.searchText, 'name'),'sort', -1);
            },
            copyAddress : function () {
                var copyText = document.getElementById("address");
                copyText.select();
                document.execCommand("Copy");
                functions.showAlert('success', 'Copied');
            },
            getWalletAddress : function () {
                var _this = this;
                setTimeout(function () {
                    axios.get('/api/getWalletAddress/' + _this.depositModal.coin.id).then(function (res){

                        var data = res.data;

                        if(data.error) {
                            return _this.getWalletAddress();
                        }

                        _this.depositModal.wallet = {};
                        _this.depositModal.wallet.address = data.data;
                        _this.depositModal.wallet.qrcode = null;
                        _this.isNotCreated = false;


                    });
                },1000);
            },
            showDeposit : function (coin,wallet = false) {
                this.depositModal.coin = coin;
                this.depositModal.wallet = wallet;
            },
            showWithdraw : function (coin,fee,wallet = false) {
                this.withdrawModal.coin = coin;
                this.withdrawModal.wallet = wallet;
                this.withdrawModal.fee = fee;
                this.withdrawModal._token = window._token;
            },
            showCacelWithdrawModal : function (id) {
                this.cancelWithdrawModal._token = window._token;
                this.cancelWithdrawModal.id = id;
            },
            createNewAddress : function (e) {

                var _this = this;

                $(e.target).attr('disabled', 'disabled');
                $(e.target).addClass('btn-loading');

                axios.get('/api/createNewAddress/' + this.depositModal.coin.id).then(function (res){
                    var data = res.data;

                    if(data.error) return functions.showAlert('error', data.error.message);

                    _this.getWalletAddress();

                });
            },
            calcFee () {
                this.withdrawModal.receive = parseFloat(this.withdrawModal.wallet.amount) - parseFloat(this.withdrawModal.fee);
                this.withdrawModal.receive = this.cutFloatNumber(this.withdrawModal.receive);
            },
            formatFloatNumber (number) {
                return functions.formatFloatNumber(number);
            },
            cutFloatNumber (number) {
                return functions.cutFloatNumber(number);
            },
            withdrawAll (number) {
                this.withdrawModal.wallet.amount = parseFloat(number);
                this.calcFee();
            },
            showTransactionDetail(element,id,$event) {
                var button = $($event.target);
                var element = $("." + element + "-" + id);
                var display = element.css('display');

                if(display == 'none') {
                    button.addClass('view-history-active');
                    button.html('hide');
                    element.css('display', 'table-row');
                } else {
                    button.removeClass('view-history-active');
                    button.html('show');
                    element.css('display', 'none');
                }
            },
            hideZero () {
                if(this.hideZeroBalance == false) {
                    $(".zero-balance").hide();
                } else {
                    $(".zero-balance").show();
                }
            },
            clickSendSmsButton () {
                var _this = this;
                axios.post('/api/sendSmsVerification', {phoneNumber: _this.smsVerification.phoneNumber, country: _this.smsVerification.country, _token: window._token}).then(function (res) {
                    var data = res.data;
                    if(data.error) {
                        return functions.showAlert('error', data.error.message);
                    }
                    if(data.success) {
                        functions.showAlert('success', data.success.message);
                    }
                });
            },
            clickSendEmailButton () {
                var _this = this;
                axios.get('/api/sendEmailVerification').then(function (res) {
                    var data = res.data;
                    if(data.error) {
                        return functions.showAlert('error', data.error.message);
                    }
                    if(data.success) {
                        functions.showAlert('success', data.success.message);
                    }
                });
            },
        }
    }
</script>
