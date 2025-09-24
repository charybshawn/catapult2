<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import Checkbox from '@/Components/Checkbox.vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { XMarkIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['close', 'switch-to-login']);

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    terms: false,
});

const isLoading = computed(() => form.processing);

const submit = () => {
    form.post(route('register'), {
        onFinish: () => {
            form.reset('password', 'password_confirmation');
        },
        onSuccess: () => {
            emit('close');
        },
    });
};

const close = () => {
    form.reset();
    form.clearErrors();
    emit('close');
};

const switchToLogin = () => {
    form.reset();
    form.clearErrors();
    emit('switch-to-login');
};
</script>

<template>
    <Modal :show="show" @close="close" max-width="md">
        <div class="relative p-6 sm:p-8 min-h-screen sm:min-h-0 flex flex-col justify-center">
            <!-- Close Button - Only on Desktop -->
            <button
                @click="close"
                class="hidden sm:block absolute top-4 right-4 p-2 rounded-lg hover:bg-white/10 transition-colors duration-200"
            >
                <XMarkIcon class="w-5 h-5 text-white/70 hover:text-white" />
            </button>

            <!-- Logo and Header -->
            <div class="text-center mb-8 sm:mb-8">
                <!-- Logo for Mobile -->
                <div class="mb-6 sm:hidden">
                    <ApplicationLogo class="h-20 w-auto mx-auto" />
                </div>

                <h2 class="text-3xl sm:text-2xl font-bold text-white mb-2">Create Account</h2>
                <p class="text-white/70 text-base sm:text-sm">Join us to access the management system</p>
            </div>

            <!-- Register Form -->
            <form @submit.prevent="submit" class="space-y-5 sm:space-y-6">
                <!-- Name Field -->
                <div>
                    <InputLabel for="name" value="Full Name" class="text-white/90 mb-2 text-base sm:text-sm" />
                    <TextInput
                        id="name"
                        type="text"
                        v-model="form.name"
                        :disabled="isLoading"
                        placeholder="Enter your full name"
                        variant="blue"
                        autofocus
                        class="h-12 sm:h-10 text-base sm:text-sm"
                    />
                    <InputError :message="form.errors.name" class="mt-2" />
                </div>

                <!-- Email Field -->
                <div>
                    <InputLabel for="email" value="Email Address" class="text-white/90 mb-2 text-base sm:text-sm" />
                    <TextInput
                        id="email"
                        type="email"
                        v-model="form.email"
                        :disabled="isLoading"
                        placeholder="Enter your email"
                        variant="blue"
                        class="h-12 sm:h-10 text-base sm:text-sm"
                    />
                    <InputError :message="form.errors.email" class="mt-2" />
                </div>

                <!-- Password Field -->
                <div>
                    <InputLabel for="password" value="Password" class="text-white/90 mb-2 text-base sm:text-sm" />
                    <TextInput
                        id="password"
                        type="password"
                        v-model="form.password"
                        :disabled="isLoading"
                        placeholder="Create a secure password"
                        variant="blue"
                        class="h-12 sm:h-10 text-base sm:text-sm"
                    />
                    <InputError :message="form.errors.password" class="mt-2" />
                </div>

                <!-- Confirm Password Field -->
                <div>
                    <InputLabel for="password_confirmation" value="Confirm Password" class="text-white/90 mb-2 text-base sm:text-sm" />
                    <TextInput
                        id="password_confirmation"
                        type="password"
                        v-model="form.password_confirmation"
                        :disabled="isLoading"
                        placeholder="Confirm your password"
                        variant="blue"
                        class="h-12 sm:h-10 text-base sm:text-sm"
                    />
                    <InputError :message="form.errors.password_confirmation" class="mt-2" />
                </div>

                <!-- Terms Acceptance -->
                <div>
                    <label class="flex items-start space-x-4 sm:space-x-3 cursor-pointer">
                        <Checkbox
                            name="terms"
                            v-model:checked="form.terms"
                            :disabled="isLoading"
                            class="mt-1 w-6 h-6 sm:w-4 sm:h-4 flex-shrink-0"
                        />
                        <div class="flex-1">
                            <span class="text-base sm:text-sm text-white/80 leading-relaxed select-none">
                                I agree to the
                                <a href="#" class="text-blue-400 hover:text-blue-300 transition-colors duration-200 underline min-h-[44px] sm:min-h-0 inline-block py-1 sm:py-0">
                                    Terms of Service
                                </a>
                                and
                                <a href="#" class="text-blue-400 hover:text-blue-300 transition-colors duration-200 underline min-h-[44px] sm:min-h-0 inline-block py-1 sm:py-0">
                                    Privacy Policy
                                </a>
                            </span>
                            <InputError :message="form.errors.terms" class="mt-1" />
                        </div>
                    </label>
                </div>

                <!-- Submit Button -->
                <PrimaryButton
                    type="submit"
                    :disabled="isLoading || !form.terms"
                    variant="blue"
                    size="large"
                    class="w-full h-14 sm:h-auto min-h-[44px]"
                >
                    <span v-if="!isLoading">Create Account</span>
                    <span v-else class="flex items-center justify-center">
                        <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Creating Account...
                    </span>
                </PrimaryButton>

                <!-- General Error -->
                <div v-if="form.errors.general" class="text-center">
                    <InputError :message="form.errors.general" />
                </div>
            </form>

            <!-- Login Link -->
            <div class="mt-8 text-center pt-6 border-t border-white/10">
                <p class="text-white/70 text-base sm:text-sm leading-relaxed">
                    Already have an account?
                </p>
                <button
                    @click="switchToLogin"
                    class="mt-3 sm:mt-1 sm:inline text-blue-400 hover:text-blue-300 transition-colors duration-200 font-medium text-base sm:text-sm min-h-[44px] sm:min-h-0 py-2 sm:py-0 px-4 sm:px-0 rounded-lg sm:rounded-none hover:bg-white/5 sm:hover:bg-transparent"
                >
                    Sign in here
                </button>
            </div>
        </div>
    </Modal>
</template>