<template>
    <div class="col-md-3" style="padding-right:0px;">
        <div class="height-information">
            <div v-bind:class="getClass('/account')">
                <a href="/account"><i class="zmdi zmdi-account font-icon-information"></i><span class="information-text">INFORMATION</span></a>
            </div>
            <div v-bind:class="getClass('/user/changePassword')">
                <a href="/user/changePassword"><i class="zmdi zmdi-key font-icon-information"></i><span class="information-text">CHANGE PASSWORD</span></a>
            </div>
            <div v-bind:class="getClass('/user/twoAuth')">
                <a href="/user/twoAuth"><i class="zmdi zmdi-badge-check font-icon-information"></i><span class="information-text">TWO-FACTORY AUTHENTICATION</span></a>
            </div>
            <div v-bind:class="getClass('/user/smsVerification')">
                <a href="/user/smsVerification"><i class="zmdi zmdi-comment-alt font-icon-information"></i><span class="information-text">SMS VERIFICATION</span></a>
            </div>
            <div v-bind:class="getClass('/user/kycVerification')">
                <a href="/user/kycVerification"><i class="zmdi zmdi-accounts-list font-icon-information" style="vertical-align: -2px;"></i><span class="information-text">KYC VERIFICATION</span></a>
            </div>
            <!-- <div v-bind:class="getClass('/user/withdrawSetting')">
                <a href="/user/withdrawSetting"><i class="zmdi zmdi-lock font-icon-information"></i><span class="information-text">WITHDRAW SETTING</span></a>
            </div> -->
        </div>
    </div>
</template>

<script>
    import Vue2Filters from 'vue2-filters'

    export default {
        data () {
            return {
                
            }
        },
        mounted () {
            
        },
        methods: {
            getClass : function (pathname) {
                if(window.location.pathname == pathname) return "information-box";
                return "information-box-s";
            }
        }
    }
</script>