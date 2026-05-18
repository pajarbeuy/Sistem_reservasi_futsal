<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';

const props = defineProps({
    status: {
        type: String,
    },
});

const form = useForm({});
const isCheckingVerification = ref(false);
let verificationTimer = null;

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(
    () => props.status === 'verification-link-sent',
);

const redirectIfVerified = async () => {
    if (isCheckingVerification.value) {
        return;
    }

    isCheckingVerification.value = true;

    try {
        const response = await window.axios.get(route('verification.status'));

        if (response.data.verified) {
            window.clearInterval(verificationTimer);
            router.visit(route('dashboard', { verified: 1 }));
        }
    } finally {
        isCheckingVerification.value = false;
    }
};

onMounted(() => {
    redirectIfVerified();
    verificationTimer = window.setInterval(redirectIfVerified, 3000);
});

onBeforeUnmount(() => {
    window.clearInterval(verificationTimer);
});
</script>

<template>
    <GuestLayout>
        <Head title="Email Verification" />

        <div class="mb-4 text-sm text-gray-600">
            Thanks for signing up! Before getting started, could you verify your
            email address by clicking on the link we just emailed to you? If you
            didn't receive the email, we will gladly send you another.
        </div>

        <div
            class="mb-4 text-sm font-medium text-green-600"
            v-if="verificationLinkSent"
        >
            A new verification link has been sent to the email address you
            provided during registration.
        </div>

        <div class="mb-4 text-sm text-gray-500">
            After you verify from Mailpit, this page will continue automatically.
        </div>

        <form @submit.prevent="submit">
            <div class="mt-4 flex items-center justify-between">
                <PrimaryButton
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Resend Verification Email
                </PrimaryButton>

                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >Log Out</Link
                >
            </div>
        </form>
    </GuestLayout>
</template>
