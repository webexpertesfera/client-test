const puppeteer = require('puppeteer');
(async () => {
    console.log("ABCDE");
    const browser = await puppeteer.launch({headless: false});     
    // const page = await browser.newPage();
    // await page.goto('http://google.com');
    // const button = await page.$('button#button');
    // await button.evaluate( button => button.click() );
    // await page.waitForTimeout(5000);
    // await browser.close();
})();