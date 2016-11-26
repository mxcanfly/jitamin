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

/**
 * Class ProjectSettingsController
 */
class ProjectSettingsController extends BaseController
{
    /**
     * Show the project information page
     *
     * @access public
     */
    public function show()
    {
        $project = $this->getProject();

        $this->response->html($this->helper->layout->project('project_settings/show', [
            'project' => $project,
            'stats' => $this->projectModel->getTaskStats($project['id']),
            'title' => $project['name'],
        ]));
    }

    /**
     * Public access management
     *
     * @access public
     */
    public function share()
    {
        $project = $this->getProject();

        $this->response->html($this->helper->layout->project('project_settings/share', [
            'project' => $project,
            'title' => t('Public access'),
        ]));
    }

    /**
     * Change project sharing
     *
     * @throws \Hiject\Core\Controller\AccessForbiddenException
     * @throws \Hiject\Core\Controller\PageNotFoundException
     */
    public function updateSharing()
    {
        $project = $this->getProject();
        $this->checkCSRFParam();
        $switch = $this->request->getStringParam('switch');

        if ($this->projectModel->{$switch.'PublicAccess'}($project['id'])) {
            $this->flash->success(t('Project updated successfully.'));
        } else {
            $this->flash->failure(t('Unable to update this project.'));
        }

        $this->response->redirect($this->helper->url->to('ProjectSettingsController', 'share', ['project_id' => $project['id']]));
    }

    /**
     * Integrations page
     *
     * @access public
     */
    public function integrations()
    {
        $project = $this->getProject();

        $this->response->html($this->helper->layout->project('project_settings/integrations', [
            'project' => $project,
            'title' => t('Integrations'),
            'webhook_token' => $this->configModel->get('webhook_token'),
            'values' => $this->projectMetadataModel->getAll($project['id']),
            'errors' => [],
        ]));
    }

    /**
     * Update integrations
     *
     * @throws \Hiject\Core\Controller\PageNotFoundException
     */
    public function updateIntegrations()
    {
        $project = $this->getProject();

        $this->projectMetadataModel->save($project['id'], $this->request->getValues());
        $this->flash->success(t('Project updated successfully.'));
        $this->response->redirect($this->helper->url->to('ProjectSettingsController', 'integrations', ['project_id' => $project['id']]));
    }

    /**
     * Display project notifications
     *
     * @access public
     */
    public function notifications()
    {
        $project = $this->getProject();

        $this->response->html($this->helper->layout->project('project_settings/notifications', [
            'notifications' => $this->projectNotificationModel->readSettings($project['id']),
            'types' => $this->projectNotificationTypeModel->getTypes(),
            'project' => $project,
            'title' => t('Notifications'),
        ]));
    }

    /**
     * Update notifications
     *
     * @throws \Hiject\Core\Controller\PageNotFoundException
     */
    public function updateNotifications()
    {
        $project = $this->getProject();
        $values = $this->request->getValues();

        $this->projectNotificationModel->saveSettings($project['id'], $values);
        $this->flash->success(t('Project updated successfully.'));
        $this->response->redirect($this->helper->url->to('ProjectSettingsController', 'notifications', ['project_id' => $project['id']]));
    }

    /**
     * Duplicate a project
     *
     * @author Antonio Rabelo
     * @author Michael Lüpkes
     * @access public
     */
    public function duplicate()
    {
        $project = $this->getProject();

        $this->response->html($this->helper->layout->project('project_settings/duplicate', [
            'project' => $project,
            'title' => t('Clone this project')
        ]));
    }

    /**
     * Do project duplication
     */
    public function doDuplication()
    {
        $project = $this->getProject();
        $project_id = $this->projectDuplicationModel->duplicate($project['id'], array_keys($this->request->getValues()), $this->userSession->getId());

        if ($project_id !== false) {
            $this->flash->success(t('Project cloned successfully.'));
        } else {
            $this->flash->failure(t('Unable to clone this project.'));
        }

        $this->response->redirect($this->helper->url->to('ProjectSettingsController', 'show', ['project_id' => $project_id]));
    }
}
