<script setup>
import { onMounted, ref } from 'vue';

const props = defineProps({
    type: {
        type: String,
        default: 'text'
    },
    placeholder: {
        type: String,
        default: ''
    },
    disabled: {
        type: Boolean,
        default: false
    },
    size: {
        type: String,
        default: 'medium',
        validator: (value) => ['small', 'medium', 'large'].includes(value)
    },
    variant: {
        type: String,
        default: 'default',
        validator: (value) => ['default', 'blue', 'green', 'purple', 'orange', 'yellow'].includes(value)
    }
});

const model = defineModel({
    type: String,
    required: true,
});

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });

const getSizeClasses = (size) => {
    const sizeMap = {
        'small': 'px-3 py-2 text-sm',
        'medium': 'px-4 py-3 text-base',
        'large': 'px-6 py-4 text-lg'
    };
    return sizeMap[size];
};

const getVariantClasses = (variant) => {
    if (variant === 'default') {
        return 'focus:border-blue-400/50 focus:ring-blue-400/20 focus:shadow-[0_4px_16px_0_rgba(59,130,246,0.2)]';
    }

    const colorMap = {
        'blue': 'focus:border-blue-400/50 focus:ring-blue-400/20 focus:shadow-[0_4px_16px_0_rgba(59,130,246,0.2)]',
        'green': 'focus:border-green-400/50 focus:ring-green-400/20 focus:shadow-[0_4px_16px_0_rgba(34,197,94,0.2)]',
        'purple': 'focus:border-purple-400/50 focus:ring-purple-400/20 focus:shadow-[0_4px_16px_0_rgba(147,51,234,0.2)]',
        'orange': 'focus:border-orange-400/50 focus:ring-orange-400/20 focus:shadow-[0_4px_16px_0_rgba(249,115,22,0.2)]',
        'yellow': 'focus:border-yellow-400/50 focus:ring-yellow-400/20 focus:shadow-[0_4px_16px_0_rgba(234,179,8,0.2)]'
    };

    return colorMap[variant];
};
</script>

<template>
    <input
        :type="type"
        :placeholder="placeholder"
        :disabled="disabled"
        v-model="model"
        ref="input"
        :class="[
            'w-full rounded-xl bg-white/10 backdrop-blur-xl border border-white/20',
            'text-white placeholder-white/60 transition-all duration-200',
            'shadow-[0_4px_12px_0_rgba(31,38,135,0.15)]',
            'focus:ring-2 focus:outline-none',
            getSizeClasses(size),
            getVariantClasses(variant),
            disabled ? 'opacity-50 cursor-not-allowed' : '',
            $attrs.class
        ]"
        style="backdrop-filter: blur(20px) saturate(150%);"
    />
</template>
