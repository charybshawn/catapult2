<template>
    <img
        src="/images/catapult-logo.png"
        alt="Catapult Logo"
        :class="className"
        class="object-contain"
    />
</template>

<script setup>
defineProps({
    className: {
        type: String,
        default: ''
    }
})
</script>