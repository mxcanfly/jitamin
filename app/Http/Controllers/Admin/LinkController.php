<?php

/*
 * This file is part of Hiject.
 *
 * Copyright (C) 2016 Hiject Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hiject\Controller;

use Hiject\Core\Controller\PageNotFoundException;

/**
 * Link Controller
 */
class LinkController extends BaseController
{
    /**
     * Get the current link
     *
     * @access private
     * @return array
     * @throws PageNotFoundException
     */
    private function getLink()
    {
        $link = $this->linkModel->getById($this->request->getIntegerParam('link_id'));

        if (empty($link)) {
            throw new PageNotFoundException();
        }

        return $link;
    }

    /**
     * List of links
     *
     * @access public
     * @param array $values
     * @param array $errors
     */
    public function index(array $values = [], array $errors = [])
    {
        $this->response->html($this->helper->layout->config('link/index', [
            'links' => $this->linkModel->getMergedList(),
            'values' => $values,
            'errors' => $errors,
            'title' => t('Settings').' &raquo; '.t('Task\'s links'),
        ]));
    }

    /**
     * Validate and save a new link
     *
     * @access public
     */
    public function save()
    {
        $values = $this->request->getValues();
        list($valid, $errors) = $this->linkValidator->validateCreation($values);

        if ($valid) {
            if ($this->linkModel->create($values['label'], $values['opposite_label']) !== false) {
                $this->flash->success(t('Link added successfully.'));
                return $this->response->redirect($this->helper->url->to('LinkController', 'index'));
            } else {
                $this->flash->failure(t('Unable to create your link.'));
            }
        }

        return $this->index($values, $errors);
    }

    /**
     * Edit form
     *
     * @access public
     * @param array $values
     * @param array $errors
     * @throws PageNotFoundException
     */
    public function edit(array $values = [], array $errors = [])
    {
        $link = $this->getLink();
        $link['label'] = t($link['label']);

        $this->response->html($this->helper->layout->config('link/edit', [
            'values' => $values ?: $link,
            'errors' => $errors,
            'labels' => $this->linkModel->getList($link['id']),
            'link' => $link,
            'title' => t('Link modification')
        ]));
    }

    /**
     * Edit a link (validate the form and update the database)
     *
     * @access public
     */
    public function update()
    {
        $values = $this->request->getValues();
        list($valid, $errors) = $this->linkValidator->validateModification($values);

        if ($valid) {
            if ($this->linkModel->update($values)) {
                $this->flash->success(t('Link updated successfully.'));
                return $this->response->redirect($this->helper->url->to('LinkController', 'index'));
            } else {
                $this->flash->failure(t('Unable to update your link.'));
            }
        }

        return $this->edit($values, $errors);
    }

    /**
     * Confirmation dialog before removing a link
     *
     * @access public
     */
    public function confirm()
    {
        $link = $this->getLink();

        $this->response->html($this->helper->layout->config('link/remove', [
            'link' => $link,
            'title' => t('Remove a link')
        ]));
    }

    /**
     * Remove a link
     *
     * @access public
     */
    public function remove()
    {
        $this->checkCSRFParam();
        $link = $this->getLink();

        if ($this->linkModel->remove($link['id'])) {
            $this->flash->success(t('Link removed successfully.'));
        } else {
            $this->flash->failure(t('Unable to remove this link.'));
        }

        $this->response->redirect($this->helper->url->to('LinkController', 'index'));
    }
}
