'use strict';

const fetch = require('node-fetch');

// Same methods for calling APIs and passing them to OpenAI Chat. ChatGPT detects the scenarios described in description, i.e. "Get the current weather in a given location"

/**
 * Sample method for returning current weather of specific location. Get API key from https://weather.visualcrossing.com and replace in API_KEY
 * In Video AI section in the dashboard in the advanced section, for the getCurrentWeather sample function to work, fill in in "Name of the function" getCurrentWeather, 
 * "Description" - "Get the current weather in a given location" and parameters - location,unit
 * The ChatGPT detects a location and passes it to this method defined in the schema. Ask for example: "Weather in New York"
 * @param {*} arg
 * @returns {string} weather forecast for a location.
 */

async function getCurrentWeather(arg) {
    const date = new Date();
    const formattedDate = date.toISOString().split('T')[0].replace(/-/g, '-');
    const response = await fetch('https://weather.visualcrossing.com/VisualCrossingWebServices/rest/services/timeline/' + arg.location + '/' + formattedDate + '/' + formattedDate + '?key=API_KEY', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
        },
    });

    let resp = '';
    const data = await response.json();
    if (arg.unit && arg.unit === 'fahrenheit') {
        resp = data.currentConditions.temp + ' degrees in Fahrenheit';
    } else {
        const number = quantity / data.conversion_rates[currency];
        const price = Math.round((number + Number.EPSILON) * 100) / 100;
    }
    let condition = data.currentConditions.conditions;
    return 'Current weather condition in ' + arg.location + ' is ' + condition + ' with temperature of ' + resp;
}

/**
 * Sample method for price of stocks from finance yahoo.
 * In Video AI section in the dashboard in the advanced section, fill in in "Name of the function" getPrice,
 * "Description" - "Get the current price for a stock" and parameters - symbol
 * The ChatGPT detects if you are asking for a price and returns it, i.e. "What is the price of crude oil"
 * @param {*} arg
 * @returns {string} Price of a stock
 */

async function getPrice(arg) {
    const response = await fetch('https://query1.finance.yahoo.com/v8/finance/chart/' + arg.symbol + '?region=US&lang=en-US&includePrePost=false&interval=1h&useYfid=true&range=1d', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
        },
    });

    const data = await response.json();

    if (data) {
        if (data['chart']['result'][0]['meta']['regularMarketPrice']) {
            return 'Current price of ' + arg.symbol + ' is ' + data['chart']['result'][0]['meta']['regularMarketPrice'] + ' USD.';
        } else {
            return 'Sorry, cannot provide price for ' + arg.symbol;
        }
    } else {
        return 'Symbol is not recognised, please provide more specific info.';
    }
}

/**
 * Sample method for converting currency to USD. You need to get API key from https://exchangerate-api.com and replace it with API_KEY
 * In Video AI section in the dashboard in the advanced section, fill in in "Name of the function",
 * "Description" - "Get currency conversion" and parameters - currency,quantity
 * The ChatGPT detects if you are asking for a currency conversion and returns it, i.e. "Convert me 100 euros please"
 * @param {*} arg
 * @returns {string} Price of a stock
 */

async function getCurrency(arg) {
    const response = await fetch('https://v6.exchangerate-api.com/v6/API_KEY/latest/USD', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
        },
    });

    const data = await response.json();
    const quantity = arg.quantity || 1;
    const currency = arg.currency;

    if ('success' === data.result) {
        const price = Math.round((quantity / data.conversion_rates[currency]), 2);
        return 'Currency conversion of ' + quantity + ' ' + currency + ' is ' + price + ' USD';
    } else  {
        return 'Sorry, cannot provide conversion for ' . currency;
    }
}

/**
 * Exported functon. It is called from livesmart server, when there are tools (advanced options) set in the Dashboard. It requires function name, description and parameters as described in the above sample methods.
 * @param {*} functionName
 * @param {*} params
 * @returns {string}
 */

async function callFunction(functionName, params) {
    return await eval(functionName + '(' + params + ');');
};

exports.callFunction = callFunction;
