<?php
class SoapClientExtendedCust extends SoapClient
{
    /**
     * Sends SOAP request using a predefined XML
     *
     * Overwrites the default method SoapClient::__doRequest() to make it work
     * with multipart responses.
     *
     * @param string $request      The XML content to send
     * @param string $location The URL to request.
     * @param string $action   The SOAP action. [optional] default=''
     * @param int    $version  The SOAP version. [optional] default=1
     * @param int    $one_way  [optional] ( If one_way is set to 1, this method
     *                         returns nothing. Use this where a response is
     *                         not expected. )
     *
     * @return string The XML SOAP response.
     */
    public function __doRequest(
        $request, $location, $action, $version, $one_way = 0
    ) {
        $result = parent::__doRequest($request, $location, $action, $version, $one_way);

        $headers = $this->__getLastResponseHeaders();

        // Do we have a multipart request?
        if (preg_match('#^Content-Type:.*multipart\/.*#mi', $headers) !== 0) {
            // Make all line breaks even.
            $result = str_replace("\r\n", "\n", $result);

            // Split between headers and content.
            list(, $content) = preg_split("#\n\n#", $result);
            // Split again for multipart boundary.
            list($result, ) = preg_split("#\n--#", $content);
        }

        return $result;
    }
}
?>