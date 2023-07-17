const { chromium } = require('playwright');

(async () => {
    const url = process.argv[2]; // Get the URL from the command-line argument

    if (!url) {
        console.error('URL argument is missing. Please provide a valid URL.');
        return;
    }

    const browser = await chromium.launch();
    const context = await browser.newContext();
    const page = await context.newPage();

    const response = await page.goto(url);
    console.log(await page.title());
    console.log(await page.url());
    //console.log(await page.content());

    // Check if there was a redirect
    if (response.status() >= 300 && response.status() <= 399) {
        const redirectUrl = response.headers().location;
        if (redirectUrl) {
            await page.goto(redirectUrl);
        } else {
            console.error('Redirect URL is missing in the response headers.');
        }
    }

    const linkSelector = 'a[href*="login"]';
    await page.waitForSelector(linkSelector);
    await page.click(linkSelector);
    await page.waitForNavigation();
    // You can perform further actions here, such as interacting with the page, taking screenshots, etc.

    await browser.close();
})();
