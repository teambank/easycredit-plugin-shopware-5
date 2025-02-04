import { request } from "@playwright/test";
import config from './playwright.config';

const username = "demo";
const apiKey = "dVHnNzuVi4wvTcwV36K12D0OFgqvVzTxsRvTmRqC";

const headers = {
  "Content-Type": "application/json",
  Accept: "application/json",
  Authorization: "Basic " + btoa(username + ":" + apiKey),
};


async function createApiClient() {
  console.log(config.use.baseURL);
  const req = await request.newContext({
    baseURL: config.use.baseURL,
  });
  return req;
}

export { createApiClient, headers as apiDefaultHeaders };
