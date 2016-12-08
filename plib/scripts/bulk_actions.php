<?php

//init task
pm_Context::init('spamexperts-extension');

array_shift($argv);

if (!empty($argv['protect']) && ($argv['unprotect'])) {
    $argv['protect'] = array_diff($argv['protect'], $argv['unprotect']);
}

if (!empty($argv['protect'])) {
    foreach ($argv['protect'] as $domain) {
        try {
            $pleskDomain = new Modules_SpamexpertsExtension_Plesk_Domain($domain);

            $protectorClass =
                Modules_SpamexpertsExtension_Plesk_Domain::TYPE_ALIAS == $pleskDomain->getType()
                    ? Modules_SpamexpertsExtension_Plesk_Domain_Strategy_Protection_Secondary::class
                    : Modules_SpamexpertsExtension_Plesk_Domain_Strategy_Protection_Primary::class;

            /** @var Modules_SpamexpertsExtension_Plesk_Domain_Strategy_Abstract $protector */
            $protector = new $protectorClass(
                $pleskDomain->getDomain(),
                $pleskDomain->getType(),
                $pleskDomain->getId()
            );
            $protector->execute();

            $messages[] = [
                'status' => 'info',
                'content' => sprintf(
                    "Domain '%s' has been successfully protected",
                    htmlentities($pleskDomain->getDomain(), ENT_QUOTES, 'UTF-8')
                ),
            ];
        } catch (Modules_SpamexpertsExtension_Exception_IncorrectStatusException $e) {
            $messages[] = [
                'status' => 'warning',
                'content' => $e->getMessage(),
            ];
        } catch (Exception $e) {
            $messages[] = [
                'status' => 'error',
                'content' => $e->getMessage(),
            ];
        }
    }
}

if (!empty($args['unprotect'])) {
    foreach ($argv['unprotect'] as $domain) {
        try {
            $pleskDomain = new Modules_SpamexpertsExtension_Plesk_Domain($domain);

            if (!$this->hasAccessToDomain($pleskDomain)) {
                $this->_status->addMessage(
                    'error',
                    sprintf('Access denied to the domain %s',
                        htmlentities($pleskDomain->getDomain(), ENT_QUOTES, 'UTF-8'))
                );
                $this->_forward('index', 'index');

                return;
            }

            $unprotectorClass =
                Modules_SpamexpertsExtension_Plesk_Domain::TYPE_ALIAS == $pleskDomain->getType()
                    ? Modules_SpamexpertsExtension_Plesk_Domain_Strategy_Unprotection_Secondary::class
                    : Modules_SpamexpertsExtension_Plesk_Domain_Strategy_Unprotection_Primary::class;

            /** @var Modules_SpamexpertsExtension_Plesk_Domain_Strategy_Abstract $unprotector */
            $unprotector = new $unprotectorClass(
                $pleskDomain->getDomain(),
                $pleskDomain->getType(),
                $pleskDomain->getId()
            );
            $unprotector->execute();

            $messages[] = [
                'status' => 'info',
                'content' => sprintf(
                    "Domain '%s' has been successfully unprotected",
                    htmlentities($pleskDomain->getDomain(), ENT_QUOTES, 'UTF-8')
                ),
            ];
        } catch (Modules_SpamexpertsExtension_Exception_IncorrectStatusException $e) {
            $messages[] = [
                'status' => 'warning',
                'content' => $e->getMessage(),
            ];
        } catch (Exception $e) {
            $messages[] = [
                'status' => 'error',
                'content' => $e->getMessage(),
            ];
        }
    }
}

$task = pm_Scheduler::getInstance()->getTaskById(pm_Settings::get('task-id'));
if (!empty($task)) {
    $task->setArguments(["protect" => null, "unprotect" => null]);
}

// TO DO: remove this after tests
$myfile = fopen("/tmp/tasktext.txt", "a+") or die("Unable to open file!");


fwrite($myfile, implode("-", $argv)."\n");

foreach($messages as $msg) {
    fwrite($myfile, $msg["status"].": ".$msg["content"]."\n");
}
fwrite($myfile, "\n");
fclose($myfile);


