<script setup>
import { Card, CardContent, CardHeader } from '@/Components/ui/card'
</script>

<template>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <Card class="w-full sm:max-w-md mt-6 p-6">
            <CardHeader>
                <slot name="card-header"/>
            </CardHeader>
            <CardContent>
                <slot />
            </CardContent>
        </Card>
    </div>
</template>
