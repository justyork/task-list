<?php
/**
 * Author: York
 * Email: yorkshp@gmail.com
 * Date: 25.04.2020
 */

namespace Controllers;


use Core\Auth;
use Core\BaseController;
use Libs\Pagination;
use Models\Task;

class IndexController extends BaseController
{

    public function access()
    {
        return [['edit', 'check'], '@'];
    }

    public function index()
    {
        $count = Task::model()->count();

        $pagination = (new Pagination())
            ->setPageSize(3)
            ->setPage($this->request->get('page', 1))
            ->setCount($count);

        // Сортировка
        $sort = $this->request->get('sort', false);
        $arr = [
            'limit' => $pagination->limit,
            'offset' => $pagination->offset,
        ];
        if ($sort) {
            $arr['order'] = $this->request->get('sort') . ' ' . ($this->request->get('desc', false) ? 'desc' : 'asc');
        }
        $tasks = Task::model()->findAll($arr);

        $newTask = new Task();
        if ($this->request->post('Task')) {
            $newTask->setAttributes($this->request->post('Task'));
            if ($newTask->save())
                $this->refresh();
        }

        return $this->render('index.index', compact('tasks', 'pagination', 'newTask'));
    }


    public function check()
    {
        $model = Task::model()->findByPk($this->request->post('id'));
        if ($model) {
            $model->status = !$model->status;
            $model->save();
        }
    }

    public function edit()
    {
        $model = Task::model()->findByPk($this->request->post('id'));
        if (!$model)
            $this->notFound();
        $model->text = $this->request->post('text');
        $model->is_updated = true;
        $model->save();
    }

}
