<template>
	<v-card style="width: 100%; min-height: 50vh; max-height: 600px;">
		<div class="card-media-jumbo">
			<slot name="jumbo"></slot>
		</div>

		<v-card-title primary-title>
			<div style="width: 100%;">
				<h3>{{ service_name }}</h3>
				<v-layout align-center justify-center>
					<slot></slot>
				</v-layout>
			</div>
		</v-card-title>
		<v-card-actions>
		</v-card-actions>
	</v-card>
</template>

<script>
export default {
	props: [
		'service_name',
		'jumbo',
		'icon',
		'button_component'
	],
	name: 'service-card',
};
</script>

<style>
.card-media-jumbo img {
	width: 100%;
}
</style>