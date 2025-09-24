<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import GlassContainer from '@/Components/Layout/GlassContainer.vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Create Your Catapult Account" />

        <!-- Header Section -->
        <div class="text-center mb-8">
            <div class="flex justify-center mb-6">
                <ApplicationLogo class="w-12 h-12 text-white" />
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold text-white mb-2">
                Join Catapult
            </h1>
            <p class="text-gray-300 text-sm sm:text-base">
                Create your free account and start revolutionizing your microgreens operation today.
            </p>
        </div>

        <!-- Registration Form -->
        <GlassContainer variant="default" padding="large">
            <form @submit.prevent="submit" class="space-y-6">
                <!-- Name Field -->
                <div>
                    <InputLabel for="name" value="Full Name" class="text-gray-300 mb-2" />
                    <TextInput
                        id="name"
                        type="text"
                        v-model="form.name"
                        required
                        autofocus
                        autocomplete="name"
                        placeholder="Enter your full name"
                        variant="green"
                    />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <!-- Email Field -->
                <div>
                    <InputLabel for="email" value="Email Address" class="text-gray-300 mb-2" />
                    <TextInput
                        id="email"
                        type="email"
                        v-model="form.email"
                        required
                        autocomplete="username"
                        placeholder="Enter your email address"
                        variant="green"
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
                        autocomplete="new-password"
                        placeholder="Create a secure password"
                        variant="green"
                    />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <!-- Confirm Password Field -->
                <div>
                    <InputLabel
                        for="password_confirmation"
                        value="Confirm Password"
                        class="text-gray-300 mb-2"
                    />
                    <TextInput
                        id="password_confirmation"
                        type="password"
                        v-model="form.password_confirmation"
                        required
                        autocomplete="new-password"
                        placeholder="Confirm your password"
                        variant="green"
                    />
                    <InputError
                        class="mt-2"
                        :message="form.errors.password_confirmation"
                    />
                </div>

                <!-- Terms Notice -->
                <div class="bg-white/5 rounded-xl p-4 border border-white/10">
                    <p class="text-xs text-gray-400 leading-relaxed">
                        By creating an account, you agree to our
                        <a href="#" class="text-green-300 hover:text-green-200 transition-colors">Terms of Service</a>
                        and
                        <a href="#" class="text-green-300 hover:text-green-200 transition-colors">Privacy Policy</a>.
                        We'll help you grow your microgreens business while keeping your data secure.
                    </p>
                </div>

                <!-- Submit Button -->
                <div class="pt-4">
                    <PrimaryButton
                        variant="green"
                        size="large"
                        :class="{ 'opacity-50': form.processing }"
                        :disabled="form.processing"
                        class="w-full justify-center"
                    >
                        <span v-if="form.processing">Creating Account...</span>
                        <span v-else>Create Free Account</span>
                    </PrimaryButton>
                </div>
            </form>
        </GlassContainer>

        <!-- Login Link -->
        <div class="mt-8 text-center">
            <GlassContainer variant="default" padding="medium">
                <div class="text-gray-300">
                    Already have an account?
                    <Link
                        :href="route('login')"
                        class="ml-1 text-green-300 hover:text-green-200 font-medium transition-colors duration-200"
                    >
                        Sign in here
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
