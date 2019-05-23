<!--
  - @copyright Copyright (c) 2019 John Molakvoæ <skjnldsv@protonmail.com>
  -
  - @author John Molakvoæ <skjnldsv@protonmail.com>
  -
  - @license GNU AGPL version 3 or any later version
  -
  - This program is free software: you can redistribute it and/or modify
  - it under the terms of the GNU Affero General Public License as
  - published by the Free Software Foundation, either version 3 of the
  - License, or (at your option) any later version.
  -
  - This program is distributed in the hope that it will be useful,
  - but WITHOUT ANY WARRANTY; without even the implied warranty of
  - MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  - GNU Affero General Public License for more details.
  -
  - You should have received a copy of the GNU Affero General Public License
  - along with this program. If not, see <http://www.gnu.org/licenses/>.
  -
  -->

<template>
	<AppSidebarTab :icon="icon" :name="name" />
</template>
<script>
import AppSidebarTab from 'nextcloud-vue/dist/Components/AppSidebarTab'

export default {
	name: 'LegacyTab',
	components: {
		AppSidebarTab: AppSidebarTab
	},
	props: {
		component: {
			type: Object,
			required: true
		},
		name: {
			type: String,
			default: '',
			required: true
		},
		fileInfo: {
			type: Object,
			default: () => {},
			required: true
		}
	},
	computed: {
		icon() {
			return this.component.getIcon()
		},
		id() {
			return this.name.toLowerCase().replace(/ /g, '-')
		},
		activeTab() {
			return this.$parent.activeTab
		}
	},
	watch: {
		fileInfo(fileInfo) {
			this.setFileInfo(fileInfo)
		}
	},
	mounted() {
		this.component.$el.appendTo(this.$el)
		this.setFileInfo(this.fileInfo)
	},
	methods: {
		setFileInfo(fileInfo) {
			this.component.setFileInfo(fileInfo)
		}
	}
}
</script>
<style>
</style>