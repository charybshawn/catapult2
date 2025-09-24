<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import GlassContainer from '@/Components/Layout/GlassContainer.vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Sign In to Catapult" />

        <!-- Header Section -->
        <div class="text-center mb-8">
            <div class="flex justify-center mb-6">
                <ApplicationLogo class="w-12 h-12 text-white" />
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold text-white mb-2">
                Welcome Back
            </h1>
            <p class="text-gray-300 text-sm sm:text-base">
                Sign in to your Catapult account to continue managing your microgreens operation.
            </p>
        </div>

        <!-- Status Message -->
        <div v-if="status" class="mb-6">
            <GlassContainer variant="green" padding="small">
                <div class="text-sm font-medium text-green-200 text-center">
                    {{ status }}
                </div>
            </GlassContainer>
        </div>

        <!-- Login Form -->
        <GlassContainer variant="default" padding="large">
            <form @submit.prevent="submit" class="space-y-6">
                <!-- Email Field -->
                <div>
                    <InputLabel for="email" value="Email Address" class="text-gray-300 mb-2" />
                    <TextInput
                        id="email"
                        type="email"
                        v-model="form.email"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="Enter your email"
                        variant="blue"
                    />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <!-- Password Field -->
                <div>
                    <InputLabel for="password" value="Password" class="text-gray-300 mb-2" />
                    <TextInput
                        id="password"
                        type="password"
                        v-model="form.password"
                        required
                        autocomplete="current-password"
                        placeholder="Enter your password"
                        variant="blue"
                    />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <!-- Remember Me Checkbox -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <Checkbox name="remember" v-model:checked="form.remember" />
                        <span class="ml-2 text-sm text-gray-300">Remember me</span>
                    </label>

                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-sm text-blue-300 hover:text-blue-200 transition-colors duration-200"
                    >
                        Forgot password?
                    </Link>
                </div>

                <!-- Submit Button -->
                <div class="pt-4">
                    <PrimaryButton
                        variant="blue"
                        size="large"
                        :class="{ 'opacity-50': form.processing }"
                        :disabled="form.processing"
                        class="w-full justify-center"
                    >
                        <span v-if="form.processing">Signing In...</span>
                        <span v-else>Sign In</span>
                    </PrimaryButton>
                </div>
            </form>
        </GlassContainer>

        <!-- Register Link -->
        <div class="mt-8 text-center">
            <GlassContainer variant="default" padding="medium">
                <div class="text-gray-300">
                    Don't have an account?
                    <Link
                        :href="route('register')"
                        class="ml-1 text-blue-300 hover:text-blue-200 font-medium transition-colors duration-200"
                    >
                        Create a free account
                    </Link>
                </div>
            </GlassContainer>
        </div>

        <!-- Back to Home -->
        <div class="mt-6 text-center">
            <Link
                :href="route('welcome')"
                class="inline-flex items-center text-sm text-gray-400 hover:text-gray-300 transition-colors duration-200"
            >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Home
            </Link>
        </div>
    </GuestLayout>
</template>
