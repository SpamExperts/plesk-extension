<?php

    $task = pm_Scheduler::getInstance()->getTaskById(pm_Settings::get('task-id'));
    pm_Scheduler::getInstance()->removeTask($task);
