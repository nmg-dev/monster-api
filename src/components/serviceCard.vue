<template>
	<v-list-tile avatar>
		<v-list-tile-avatar>
			<slot name="avatar"></slot>
		</v-list-tile-avatar>
		<v-list-tile-content class="service-content">
			<slot></slot>
		</v-list-tile-content>
	</v-list-tile>
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
.service-content {
	align-items: center;
}
.service-content img {
	width: 240px;
	object-fit: cover;
	cursor: pointer;
}
</style>