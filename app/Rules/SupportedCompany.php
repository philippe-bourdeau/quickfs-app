<?php

namespace App\Rules;

use App\Business\Clients\IQuickFSClient;
use Illuminate\Contracts\Validation\Rule;

class SupportedCompany implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        /** @var IQuickFSClient $client */
        $client = app(IQuickFSClient::class);

        $response = $client->singleMetric(
            $value,
            'price',
            ''
        );

        $contents = \GuzzleHttp\json_decode($response->getBody()->getContents());

        if ($contents->{'errors'}->{'error'} === 'UnsupportedCompanyError') {
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return '{:input} is not supported or does not exist : .';
    }
}
