<template>
    <div>
        <el-table :data="state.common.versions" class="w100" stripe>
            <el-table-column property="version" align="center" :label="t('module.Version')" />
            <el-table-column property="short_describe" :show-overflow-tooltip="true" align="center" :label="t('module.Description')" />
            <el-table-column property="available_system_version_text" align="center" :label="t('module.Available system version')">
                <template #default="scope">
                    <div v-if="scope.row.available_system_version && state.sysVersion">
                        <div class="available-system-version">
                            <Icon
                                v-if="compareVersion(scope.row.available_system_version)"
                                name="el-icon-CircleCheckFilled"
                                color="var(--el-color-success)"
                                size="14"
                            />
                            <Icon v-else name="el-icon-CircleCloseFilled" size="14" color="var(--el-color-danger)" />
                            <div class="available-system-version-text">{{ scope.row.available_system_version_text }}</div>
                        </div>
                    </div>
                    <div v-else>-</div>
                </template>
            </el-table-column>
            <el-table-column property="createtime_text" align="center" :label="t('Create time')" />
            <el-table-column :label="t('module.Install')" align="center" :min-width="140">
                <template #default="scope">
                    <div v-if="scope.row.downloadable">
                        <div v-if="isLocalModuleVersion(scope.row.version)" class="renewal-text">{{ t('module.Current installed version') }}</div>
                        <div v-else-if="!compareVersion(scope.row.available_system_version)">{{ t('module.Insufficient system version') }}</div>
                        <div v-else>
                            <el-button type="primary" @click="onInstall(scope.row.uid, scope.row.order_id, scope.row.version)">
                                {{ t('module.Click to install') }}
                            </el-button>
                        </div>
                    </div>
                    <el-tooltip
                        v-else
                        effect="dark"
                        :content="
                            t('module.Order expiration time', {
                                expiration_time: timeFormat(scope.row.order_expiration_time),
                                create_time: timeFormat(scope.row.createtime),
                            })
                        "
                        placement="top"
                    >
                        <div class="renewal">
                            <div class="renewal-text">{{ t('module.Versions released beyond the authorization period') }}</div>
                            <el-button @click="onBuy(true)" type="danger">{{ t('module.Renewal') }}</el-button>
                        </div>
                    </el-tooltip>
                </template>
            </el-table-column>
        </el-table>
    </div>
</template>

<script setup lang="ts">
import { memoize } from 'lodash-es'
import { useI18n } from 'vue-i18n'
import { execInstall, onBuy, showCommonLoading } from '../index'
import { state } from '../store'
import { timeFormat } from '/@/utils/common'

const { t } = useI18n()

const formatSysVersion = memoize((sysVersion: string) => {
    // 去掉 sysVersion 开头的 v
    sysVersion = sysVersion.replace(/^v/, '')

    // 以 . 分割，不足两位的补 0
    sysVersion = sysVersion
        .split('.')
        .map((item) => {
            return item.padStart(2, '0')
        })
        .join('')

    return parseInt(sysVersion)
})

const isLocalModuleVersion = (version: string) => {
    const localModule = state.installedModule.find((item) => {
        return item.uid == state.common.uid
    })

    if (localModule) {
        version = version.replace(/^v/, '')
        localModule.version = localModule.version.replace(/^v/, '')
        if (version == localModule.version) {
            return true
        }
    }

    return false
}

const compareVersion = memoize((version: string): boolean => {
    const sysVersion = formatSysVersion(state.sysVersion)
    return sysVersion > parseInt(version)
})

const onInstall = (uid: string, id: number, version: string) => {
    state.dialog.common = true
    state.common.dialogTitle = t('module.Install')
    showCommonLoading('download')

    // 关闭其他弹窗
    state.dialog.baAccount = false
    state.dialog.buy = false
    state.dialog.goodsInfo = false

    execInstall(uid, id, version, state.common.update)
}
</script>

<style scoped lang="scss">
.renewal {
    display: flex;
    align-items: center;
    justify-content: center;
    .renewal-text {
        font-size: 12px;
        margin-right: 6px;
    }
}
.available-system-version {
    display: flex;
    align-items: center;
    justify-content: center;
    .available-system-version-text {
        margin-left: 4px;
    }
}
</style>
