<template>
    <form action="index.html">
        <div class="slider-form">
            <input data-animation-in="fadeInLeft" data-animation-out="animate-out fadeInLeft" type="email" name="email" placeholder="Email or Username">
            <input data-animation-in="fadeInRight" data-animation-out="animate-out fadeInRight" type="password" name="password" placeholder="Password">
        </div>
        <a data-animation-in="fadeInUp" data-animation-out="animate-out fadeInUp" href="" class="btn btn-register">Login</a>
        <a data-animation-in="fadeInUp" data-animation-out="animate-out fadeInUp" href="" class="btn btn-view">view markets</a>
    </form>
</template>

<script>
    export default {
        mounted() {
            console.log('Component mounted.')
        }
    }
</script>
