<?php

namespace app\admin\library\traits;

use Throwable;

/**
 * 后台控制器trait类
 * 已导入到 @see \app\common\controller\Backend 中
 * 若需修改此类方法：请复制方法至对应控制器后进行重写
 */
trait Backend
{
    /**
     * 排除入库字段
     * @param array $params
     * @return array
     */
    protected function excludeFields(array $params): array
    {
        if (!is_array($this->preExcludeFields)) {
            $this->preExcludeFields = explode(',', (string)$this->preExcludeFields);
        }

        foreach ($this->preExcludeFields as $field) {
            if (array_key_exists($field, $params)) {
                unset($params[$field]);
            }
        }
        return $params;
    }

    /**
     * 查看
     * @throws Throwable
     */
    public function index(): void
    {
        if ($this->request->param('select')) {
            $this->select();
        }

        list($where, $alias, $limit, $order) = $this->queryBuilder();
        $res = $this->model
            ->field($this->indexField)
            ->withJoin($this->withJoinTable, $this->withJoinType)
            ->alias($alias)
            ->where($where)
            ->order($order)
            ->paginate($limit);

        $this->success('', [
            'list'   => $res->items(),
            'total'  => $res->total(),
            'remark' => get_route_remark(),
        ]);
    }

    /**
     * 添加
     */
    public function add(): void
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            if (!$data) {
                $this->error(__('Parameter %s can not be empty', ['']));
            }

            $data = $this->excludeFields($data);
            if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
                $data[$this->dataLimitField] = $this->auth->id;
            }

            $result = false;
            $this->model->startTrans();
            try {
                // 模型验证
                if ($this->modelValidate) {
                    $validate = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                    if (class_exists($validate)) {
                        $validate = new $validate();
                        if ($this->modelSceneValidate) $validate->scene('add');
                        $validate->check($data);
                    }
                }
                $result = $this->model->save($data);
                $this->model->commit();
            } catch (Throwable $e) {
                $this->model->rollback();
                $this->error($e->getMessage());
            }
            if ($result !== false) {
                $this->success(__('Added successfully'));
            } else {
                $this->error(__('No rows were added'));
            }
        }

        $this->error(__('Parameter error'));
    }

    /**
     * 编辑
     * @throws Throwable
     */
    public function edit(): void
    {
        $pk  = $this->model->getPk();
        $id  = $this->request->param($pk);
        $row = $this->model->find($id);
        if (!$row) {
            $this->error(__('Record not found'));
        }

        $dataLimitAdminIds = $this->getDataLimitAdminIds();
        if ($dataLimitAdminIds && !in_array($row[$this->dataLimitField], $dataLimitAdminIds)) {
            $this->error(__('You have no permission'));
        }

        if ($this->request->isPost()) {
            $data = $this->request->post();
            if (!$data) {
                $this->error(__('Parameter %s can not be empty', ['']));
            }

            $data   = $this->excludeFields($data);
            $result = false;
            $this->model->startTrans();
            try {
                // 模型验证
                if ($this->modelValidate) {
                    $validate = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                    if (class_exists($validate)) {
                        $validate = new $validate();
                        if ($this->modelSceneValidate) $validate->scene('edit');
                        $data[$pk] = $row[$pk];
                        $validate->check($data);
                    }
                }
                $result = $row->save($data);
                $this->model->commit();
            } catch (Throwable $e) {
                $this->model->rollback();
                $this->error($e->getMessage());
            }
            if ($result !== false) {
                $this->success(__('Update successful'));
            } else {
                $this->error(__('No rows updated'));
            }
        }

        $this->success('', [
            'row' => $row
        ]);
    }

    /**
     * 删除
     * @throws Throwable
     */
    public function del(): void
    {
        $where             = [];
        $dataLimitAdminIds = $this->getDataLimitAdminIds();
        if ($dataLimitAdminIds) {
            $where[] = [$this->dataLimitField, 'in', $dataLimitAdminIds];
        }

        $ids     = $this->request->param('ids/a', []);
        $where[] = [$this->model->getPk(), 'in', $ids];
        $data    = $this->model->where($where)->select();

        $count = 0;
        $this->model->startTrans();
        try {
            foreach ($data as $v) {
                $count += $v->delete();
            }
            $this->model->commit();
        } catch (Throwable $e) {
            $this->model->rollback();
            $this->error($e->getMessage());
        }
        if ($count) {
            $this->success(__('Deleted successfully'));
        } else {
            $this->error(__('No rows were deleted'));
        }
    }

    /**
     * 排序 - 增量重排法
     * @throws Throwable
     */
    public function sortable(): void
    {
        $pk        = $this->model->getPk();
        $move      = $this->request->param('move');
        $target    = $this->request->param('target');
        $order     = $this->request->param("order/s") ?: $this->defaultSortField;
        $direction = $this->request->param('direction');

        $dataLimitWhere    = [];
        $dataLimitAdminIds = $this->getDataLimitAdminIds();
        if ($dataLimitAdminIds) {
            $dataLimitWhere[] = [$this->dataLimitField, 'in', $dataLimitAdminIds];
        }

        $moveRow   = $this->model->where($dataLimitWhere)->find($move);
        $targetRow = $this->model->where($dataLimitWhere)->find($target);

        if ($move == $target || !$moveRow || !$targetRow || !$direction) {
            $this->error(__('Record not found'));
        }

        // 当前是否以权重字段排序（只检查当前排序和默认排序字段，不检查有序保证字段）
        if ($order && is_string($order)) {
            $order = explode(',', $order);
            $order = [$order[0] => $order[1] ?? 'asc'];
        }
        if (!array_key_exists($this->weighField, $order)) {
            $this->error(__('Please use the %s field to sort before operating', [$this->weighField]));
        }

        // 开始增量重排
        $order = $this->queryOrderBuilder();
        $weigh = $targetRow[$this->weighField];

        // 波及行的权重值向上增加还是向下减少
        if ($order[$this->weighField] == 'desc') {
            $updateMethod = $direction == 'up' ? 'dec' : 'inc';
        } else {
            $updateMethod = $direction == 'up' ? 'inc' : 'dec';
        }

        // 与目标行权重相同的行
        $weighRowIds    = $this->model
            ->where($dataLimitWhere)
            ->where($this->weighField, $weigh)
            ->order($order)
            ->column($pk);
        $weighRowsCount = count($weighRowIds);

        // 单个 SQL 查询中完成大于目标权重行的修改
        $this->model->where($dataLimitWhere)
            ->where($this->weighField, $updateMethod == 'dec' ? '<' : '>', $weigh)
            ->whereNotIn($pk, [$moveRow->$pk])
            ->$updateMethod($this->weighField, $weighRowsCount)
            ->save();

        // 遍历与目标行权重相同的行，每出现一行权重值将额外 +1，保证权重相同行的顺序位置不变
        if ($direction == 'down') {
            $weighRowIds = array_reverse($weighRowIds);
        }

        $moveComplete = 0;
        $weighRowIds  = implode(',', $weighRowIds);
        $weighRows    = $this->model->where($dataLimitWhere)
            ->where($pk, 'in', $weighRowIds)
            ->orderRaw("field($pk,$weighRowIds)")
            ->select();

        // 权重相等行
        foreach ($weighRows as $key => $weighRow) {
            // 跳过当前拖动行（相等权重数据之间的拖动时，被拖动行会出现在 $weighRows 内）
            if ($moveRow[$pk] == $weighRow[$pk]) {
                continue;
            }

            if ($updateMethod == 'dec') {
                $rowWeighVal = $weighRow[$this->weighField] - $key;
            } else {
                $rowWeighVal = $weighRow[$this->weighField] + $key;
            }

            // 找到了目标行
            if ($weighRow[$pk] == $targetRow[$pk]) {
                $moveComplete               = 1;
                $moveRow[$this->weighField] = $rowWeighVal;
                $moveRow->save();
            }

            $rowWeighVal                 = $updateMethod == 'dec' ? $rowWeighVal - $moveComplete : $rowWeighVal + $moveComplete;
            $weighRow[$this->weighField] = $rowWeighVal;
            $weighRow->save();
        }

        $this->success();
    }

    /**
     * 加载为select(远程下拉选择框)数据，默认还是走$this->index()方法
     * 必要时请在对应控制器类中重写
     */
    public function select(): void
    {

    }

    /**
     * 导出数据
     * @throws Throwable
     */
    public function export(): void
    {
        $format = $this->request->get('format', 'excel');
        $fields = $this->request->get('fields', []);
        $search = $this->request->get('search', []);
        $order = $this->request->get('order', '');

        list($where, $alias, $limit, $order) = $this->queryBuilder();
        $data = $this->model
            ->field($this->indexField)
            ->withJoin($this->withJoinTable, $this->withJoinType)
            ->alias($alias)
            ->where($where)
            ->order($order)
            ->select();

        if (empty($data)) {
            $this->error(__('No data to export'));
        }

        // 处理导出字段
        if (!empty($fields)) {
            $exportData = [];
            foreach ($data as $item) {
                $row = [];
                foreach ($fields as $field) {
                    if (isset($item[$field])) {
                        $row[$field] = $item[$field];
                    }
                }
                $exportData[] = $row;
            }
            $data = $exportData;
        }

        // 导出文件名
        $filename = parse_name(basename(str_replace('\\', '/', get_class($this->model)))) . '_' . date('YmdHis');

        switch ($format) {
            case 'csv':
                $this->exportCsv($data, $filename);
                break;
            case 'excel':
            default:
                $this->exportExcel($data, $filename);
                break;
        }
    }

    /**
     * 导出为CSV格式
     * @param array $data 数据
     * @param string $filename 文件名
     */
    protected function exportCsv(array $data, string $filename): void
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename . '.csv');
        header('Cache-Control: max-age=0');

        $output = fopen('php://output', 'w');
        if ($output) {
            // 输出BOM，解决中文乱码
            fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));
            
            // 输出表头
            if (!empty($data)) {
                fputcsv($output, array_keys($data[0]));
                // 输出数据
                foreach ($data as $row) {
                    fputcsv($output, $row);
                }
            }
            fclose($output);
        }
        exit;
    }

    /**
     * 导出为Excel格式
     * @param array $data 数据
     * @param string $filename 文件名
     */
    protected function exportExcel(array $data, string $filename): void
    {
        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename . '.xls');
        header('Cache-Control: max-age=0');

        $output = fopen('php://output', 'w');
        if ($output) {
            // 输出BOM，解决中文乱码
            fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));
            
            // 输出表头
            if (!empty($data)) {
                fputcsv($output, array_keys($data[0]), "\t");
                // 输出数据
                foreach ($data as $row) {
                    fputcsv($output, $row, "\t");
                }
            }
            fclose($output);
        }
        exit;
    }

    /**
     * 批量操作
     * @throws Throwable
     */
    public function batchAction(): void
    {
        $action = $this->request->param('action');
        $ids = $this->request->param('ids', []);

        if (empty($action) || empty($ids)) {
            $this->error(__('Parameter error'));
        }

        $where             = [];
        $dataLimitAdminIds = $this->getDataLimitAdminIds();
        if ($dataLimitAdminIds) {
            $where[] = [$this->dataLimitField, 'in', $dataLimitAdminIds];
        }

        $where[] = [$this->model->getPk(), 'in', $ids];
        $data = $this->model->where($where)->select();

        if (empty($data)) {
            $this->error(__('No records found'));
        }

        // 执行批量操作
        $result = false;
        $this->model->startTrans();
        try {
            foreach ($data as $item) {
                switch ($action) {
                    case 'enable':
                        $item->status = 1;
                        break;
                    case 'disable':
                        $item->status = 0;
                        break;
                    // 可以添加更多批量操作类型
                }
                $item->save();
            }
            $this->model->commit();
            $result = true;
        } catch (Throwable $e) {
            $this->model->rollback();
            $this->error($e->getMessage());
        }

        if ($result) {
            $this->success(__('Batch operation successful'));
        } else {
            $this->error(__('Batch operation failed'));
        }
    }
}