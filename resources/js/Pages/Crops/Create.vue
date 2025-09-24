<template>
  <Head title="Create Crop Batch" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold text-white">Create New Crop Batch</h1>
          <p class="text-slate-300 mt-1">Start a new batch of microgreens with multiple trays</p>
        </div>
        <Link :href="route('crops.index')" as="button">
          <SecondaryButton class="flex items-center space-x-2">
            <ArrowLeftIcon class="w-4 h-4" />
            <span>Back to Crops</span>
          </SecondaryButton>
        </Link>
      </div>
    </template>

    <div class="space-y-6">
      <GlassContainer variant="default" padding="large">
        <form @submit.prevent="submit" class="space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Tray Identifiers -->
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-white mb-2">
                Tray Identifiers *
              </label>
              <div class="relative">
                <div class="w-full min-h-[42px] px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white focus-within:ring-2 focus-within:ring-blue-400/50 focus-within:border-blue-400/50 transition-colors">
                  <!-- Tag Display -->
                  <div class="flex flex-wrap gap-2 mb-2" v-if="form.tray_identifiers.length > 0">
                    <div
                      v-for="(tag, index) in form.tray_identifiers"
                      :key="index"
                      class="inline-flex items-center px-2 py-1 bg-blue-500/20 border border-blue-400/30 rounded-md text-sm text-blue-300"
                    >
                      <span>{{ tag }}</span>
                      <button
                        type="button"
                        @click="removeTag(index)"
                        class="ml-1 text-blue-300 hover:text-white transition-colors"
                        aria-label="Remove tag"
                      >
                        <XMarkIcon class="w-3 h-3" />
                      </button>
                    </div>
                  </div>

                  <!-- Input Field -->
                  <input
                    ref="tagInput"
                    v-model="currentTag"
                    @keydown="handleKeyDown"
                    @blur="addCurrentTag"
                    type="text"
                    placeholder="Enter tray ID (e.g., A1, B2, C3) and press Enter or comma"
                    class="w-full bg-transparent border-none outline-none text-white placeholder-gray-400 text-sm"
                  />
                </div>
              </div>
              <div class="mt-1 flex items-center justify-between">
                <p class="text-xs text-gray-400">
                  Enter unique identifiers for each tray. Press Enter or use comma to add multiple.
                </p>
                <p class="text-xs text-blue-300">
                  {{ form.tray_identifiers.length }} tray{{ form.tray_identifiers.length !== 1 ? 's' : '' }}
                </p>
              </div>
              <div v-if="tagError" class="mt-1">
                <p class="text-xs text-red-400">{{ tagError }}</p>
              </div>
            </div>

            <!-- Recipe (Optional) -->
            <div>
              <label class="block text-sm font-medium text-white mb-2">
                Recipe (Optional)
              </label>
              <select
                v-model="form.recipe_id"
                class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white focus:ring-2 focus:ring-blue-400/50 focus:border-blue-400/50 transition-colors"
              >
                <option value="">Select a recipe</option>
                <option v-for="recipe in recipes" :key="recipe.id" :value="recipe.id" class="bg-gray-800">
                  {{ recipe.name }} ({{ recipe.variety }})
                </option>
              </select>
              <p class="text-xs text-gray-400 mt-1">Recipe will guide timing and growing parameters</p>
            </div>

            <!-- Location -->
            <div>
              <label class="block text-sm font-medium text-white mb-2">
                Base Location *
              </label>
              <input
                v-model="form.location"
                type="text"
                class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-400/50 focus:border-blue-400/50 transition-colors"
                placeholder="Soaking Station A"
                required
              />
              <p class="text-xs text-gray-400 mt-1">Individual tray positions will be auto-generated</p>
            </div>
          </div>

          <!-- Notes -->
          <div>
            <label class="block text-sm font-medium text-white mb-2">
              Batch Notes
            </label>
            <textarea
              v-model="form.notes"
              rows="3"
              class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-400/50 focus:border-blue-400/50 transition-colors resize-none"
              placeholder="Add any notes about this batch (seed lot, special conditions, etc.)"
            ></textarea>
          </div>

          <!-- Form Actions -->
          <div class="flex items-center justify-between pt-6 border-t border-white/10">
            <Link :href="route('crops.index')" as="button">
              <SecondaryButton class="flex items-center space-x-2">
                <XMarkIcon class="w-4 h-4" />
                <span>Cancel</span>
              </SecondaryButton>
            </Link>

            <PrimaryButton
              type="submit"
              :disabled="form.processing"
              variant="green"
              class="flex items-center space-x-2"
            >
              <PlusIcon class="w-4 h-4" />
              <span v-if="form.processing">Creating...</span>
              <span v-else>Create Batch</span>
            </PrimaryButton>
          </div>

          <!-- Form Errors -->
          <div v-if="form.errors && Object.keys(form.errors).length > 0" class="rounded-lg bg-red-500/10 border border-red-500/20 p-4">
            <div class="flex">
              <ExclamationTriangleIcon class="h-5 w-5 text-red-400" />
              <div class="ml-3">
                <h3 class="text-sm font-medium text-red-300">Please fix the following errors:</h3>
                <div class="mt-2 text-sm text-red-200">
                  <ul class="list-disc space-y-1 pl-5">
                    <li v-for="(error, field) in form.errors" :key="field">
                      {{ error }}
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </form>
      </GlassContainer>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, nextTick } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import GlassContainer from '@/Components/Layout/GlassContainer.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

// Import Heroicons
import {
  PlusIcon,
  XMarkIcon,
  ArrowLeftIcon,
  ExclamationTriangleIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    recipes: {
        type: Array,
        default: () => []
    }
});

const form = useForm({
    tray_identifiers: [],
    recipe_id: '',
    location: '',
    notes: ''
});

// Tag input functionality
const currentTag = ref('');
const tagError = ref('');
const tagInput = ref(null);

const validateTag = (tag) => {
    const trimmedTag = tag.trim().toUpperCase();

    if (!trimmedTag) {
        return { valid: false, error: 'Tray identifier cannot be empty' };
    }

    if (trimmedTag.length > 10) {
        return { valid: false, error: 'Tray identifier cannot exceed 10 characters' };
    }

    if (!/^[A-Z0-9]+$/.test(trimmedTag)) {
        return { valid: false, error: 'Tray identifier can only contain letters and numbers' };
    }

    if (form.tray_identifiers.includes(trimmedTag)) {
        return { valid: false, error: 'Tray identifier already exists' };
    }

    return { valid: true, tag: trimmedTag };
};

const addTag = (tag) => {
    const validation = validateTag(tag);

    if (validation.valid) {
        form.tray_identifiers.push(validation.tag);
        currentTag.value = '';
        tagError.value = '';
        return true;
    } else {
        tagError.value = validation.error;
        return false;
    }
};

const addCurrentTag = () => {
    if (currentTag.value.trim()) {
        addTag(currentTag.value);
    }
};

const removeTag = (index) => {
    form.tray_identifiers.splice(index, 1);
    tagError.value = '';
    nextTick(() => {
        if (tagInput.value) {
            tagInput.value.focus();
        }
    });
};

const handleKeyDown = (event) => {
    tagError.value = '';

    if (event.key === 'Enter') {
        event.preventDefault();
        if (currentTag.value.trim()) {
            addTag(currentTag.value);
        }
    } else if (event.key === ',' || event.key === ';') {
        event.preventDefault();
        if (currentTag.value.trim()) {
            addTag(currentTag.value);
        }
    } else if (event.key === 'Backspace' && !currentTag.value && form.tray_identifiers.length > 0) {
        // Remove last tag if input is empty and backspace is pressed
        removeTag(form.tray_identifiers.length - 1);
    }
};

const submit = () => {
    // Clear any existing tag error
    tagError.value = '';

    // Validate that at least one tray identifier is provided
    if (form.tray_identifiers.length === 0) {
        tagError.value = 'At least one tray identifier is required';
        return;
    }

    form.post(route('crops.store'), {
        onSuccess: () => {
            // Success handled by redirect in controller
        },
        onError: (errors) => {
            console.log('Form errors:', errors);
        }
    });
};
</script>