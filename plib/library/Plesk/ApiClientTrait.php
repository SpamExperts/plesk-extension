<?php

/**
 * @SuppressWarnings(PHPMD.CamelCaseClassName)
 */
trait Modules_SpamexpertsExtension_Plesk_ApiClientTrait
{
    /**
     * Plesk XML API requests executor
     *
     * @param string $request
     *
     * @return SimpleXMLElement
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function xmlapi($request)
    {
        pm_Log::debug("Ready to make a request to XML API: $request");

        $response = pm_ApiRpc::getService()->call($request);

        pm_Log::debug("Request is finished, response is: " . $response->asXML());

        return $response;
    }

}
