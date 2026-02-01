<template>
    <el-aside v-if="!navTabs.state.tabFullScreen" :class="['layout-aside-' + config.layout.layoutMode, config.layout.shrink ? 'shrink' : '']">
        <Logo v-if="config.layout.menuShowTopBar" />
        <MenuVerticalChildren v-if="config.layout.layoutMode == 'Double'" />
        <MenuVertical v-else />
    </el-aside>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import Logo from '/@/layouts/backend/components/logo.vue'
import MenuVertical from '/@/layouts/backend/components/menus/menuVertical.vue'
import MenuVerticalChildren from '/@/layouts/backend/components/menus/menuVerticalChildren.vue'
import { useConfig } from '/@/stores/config'
import { useNavTabs } from '/@/stores/navTabs'
import { SYSTEM_ZINDEX } from '/@/stores/constant/common'

defineOptions({
    name: 'layout/aside',
})

const config = useConfig()
const navTabs = useNavTabs()

const menuWidth = computed(() => config.menuWidth())
</script>

<style scoped lang="scss">
.layout-aside-Default:not(.shrink) {
    background: var(--ba-bg-color-overlay);
    margin: 16px 0 16px 16px;
    height: calc(100% - 32px);
    box-shadow: var(--el-box-shadow-light);
    border-radius: var(--el-border-radius-base);
    overflow: hidden;
    transition: width 0.3s ease;
    width: v-bind(menuWidth);
}
.layout-aside-Default.shrink,
.layout-aside-Classic,
.layout-aside-Double {
    background: var(--ba-bg-color-overlay);
    margin: 0;
    height: 100%;
    overflow: hidden;
    transition: width 0.3s ease;
    width: v-bind(menuWidth);
}
.shrink {
    position: fixed;
    top: 0;
    left: 0;
    z-index: v-bind('SYSTEM_ZINDEX');
}
</style>
