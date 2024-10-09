import { test, expect } from "@playwright/test";
import { delay, randomize, clickWithRetry } from "./utils";
import { PaymentTypes } from "./types";

export const goToProduct = async (page, sku = "regular") => {
  await test.step(`Go to product (sku: ${sku}}`, async () => {
    await page.goto(`search?sSearch=${sku}`);
  });
};

export const addCurrentProductToCart = async (page) => {
  await page.getByRole("button", { name: "In den Warenkorb" }).first().click();
  await page.waitForResponse(/checkout\/ajaxAddArticleCart/);

  await expect(page.locator(".ajax--cart .alert")).toContainText(
    'Der Artikel wurde erfolgreich in den Warenkorb gelegt'
  );
};

export const selectAndProceed = async ({
  page,
  paymentType,
}: {
  page: any;
  paymentType: PaymentTypes;
}) => {
  await test.step(`Start standard checkout (${paymentType})`, async () => {
    if (paymentType === PaymentTypes.INSTALLMENT) {
      await page
        .locator("easycredit-checkout-label[payment-type=INSTALLMENT]")
        .click();
      await page.getByRole("button", { name: "Weiter zu easyCredit-Ratenkauf" }).click();
      return;
    }
    if (paymentType === PaymentTypes.BILL) {
      await page.locator("easycredit-checkout-label[payment-type=BILL]").click();
      await page
        .getByRole("button", { name: "Weiter zu easyCredit-Rechnung" })
        .click();
      return;
    }
  });
};

export const startExpress = async ({
  page,
  paymentType,
}: {
  page: any;
  paymentType: PaymentTypes;
}) => {
  await test.step(`Start express checkout (${paymentType})`, async () => {
    if (paymentType === PaymentTypes.INSTALLMENT) {
      await page
        .locator("a")
        .filter({ hasText: "in Raten" })
        .click();
      await page.getByText("Akzeptieren", { exact: true }).click();
    }
    if (paymentType === PaymentTypes.BILL) {
      await page.locator("a").filter({ hasText: "In 30 Tagen" }).click();
      await page.getByText("Akzeptieren", { exact: true }).click();
    }
  });
};

export const fillCheckout = async (page, isCompany = false) => {
  await test.step("Fill out standard checkout", async () => {
    const register = await page.locator("#registration");
    await register
      .locator("#register_personal_customer_type")
      .selectOption(isCompany ? "Firma" : "Privatkunde");
    if (isCompany) {
      await register.getByRole("textbox", { name: "Firma" }).fill("Firma");
    }
    await register.locator("#salutation").selectOption({ index: 1 });

    await register
      .getByRole("textbox", { name: "Vorname" })
      .fill(randomize("Ralf"));
    await register.getByRole("textbox", { name: "Nachname" }).fill("Ratenkauf");

    await register.locator("#register_personal_skipLogin").click();
    await register
      .getByRole("textbox", { name: /E-Mail/ })
      .fill("test@email.com");

    await register
      .getByRole("textbox", { name: /Straße/ })
      .fill("Beuthener Str. 25");

    await register.getByRole("textbox", { name: "PLZ" }).fill("90402");
    await register.getByRole("textbox", { name: "Ort" }).fill("Nürnberg");

    await register.getByRole("button", { name: "Weiter" }).click();
  });
};

export const goThroughPaymentPage = async ({
  page,
  paymentType,
  express = false,
}: {
  page: any;
  paymentType: PaymentTypes;
  express?: boolean;
}) => {
  await test.step(`easyCredit-Ratenkauf Payment`, async () => {
    await page.getByTestId("uc-deny-all-button").click();

    await expect(
      page.getByText(
        paymentType === PaymentTypes.INSTALLMENT
          ? "Monatliche Wunschrate"
          : "Ihre Bezahloptionen"
      )
    ).toBeVisible();

    await page.getByRole("button", { name: "Weiter zur Dateneingabe" }).click();

    if (express) {
      await page.locator("#firstName").fill(randomize("Ralf"));
      await page.locator("#lastName").fill("Ratenkauf");
    }

    await page.locator("#dateOfBirth").fill("05.04.1972");

    if (express) {
      await page.locator("#email").getByRole('textbox').fill("ralf.ratenkauf@teambank.de");
    }

    await page.locator("tbk-vorwahldropdown .tel-wrapper").click();
    await page.locator('tbk-vorwahldropdown').locator('li').filter({ hasText: '+49' }).getByRole('paragraph').click();
    await page.locator('#mobilfunknummer').getByRole('textbox').fill('1703404848');
    await page.locator('app-ratenkauf-iban-input-dumb').getByRole('textbox').fill("DE12500105170648489890");

    if (express) {
      await page.locator("#streetAndNumber").fill("Beuthener Str. 25");
      await page.locator("#postalCode").fill("90402");
      await page.locator("#city").fill("Nürnberg");
    }

    await page.locator("#agreeAll").click();

    await delay(500);
    await clickWithRetry(
      page.getByRole("button", { name: "Zahlungswunsch prüfen" })
    );

    await delay(500);
    await page.getByRole("button", { name: "Ratenwunsch übernehmen" }).click();
  });
};

export const confirmOrder = async ({
  page,
  paymentType,
}: {
  page: any;
  paymentType: PaymentTypes;
}) => {
  await test.step(`Confirm order`, async () => {
    await expect(page.locator(".payment--description")).toContainText(
      paymentType === PaymentTypes.INSTALLMENT ? "Ratenkauf" : "Rechnung"
    );

    /* temporarly disabled, waiting for implementation on api side
    if (paymentType === PaymentTypes.INSTALLMENT) {
      await expect
        .soft(page.locator(".product--table"))
        .toContainText("Zinsen für Ratenzahlung");
    } else {
      await expect
        .soft(page.locator(".product--table"))
        .not.toContainText("Zinsen für Ratenzahlung");
    }
    */

    /* Confirm Page */
    await page.locator("#sAGB").check();
    await page
      .getByRole("button", { name: "Zahlungspflichtig bestellen" })
      .click();

    /* Success Page */
    await expect(
      page.getByText("Vielen Dank für Ihre Bestellung")
    ).toBeVisible();
  });
};

export const checkAddressInvalidation = async (page) => {
  await test.step("Check if an address change invalidates payment", async () => {
    await page.waitForURL("**/checkout/confirm");

    await page.getByText("Adresse ändern").click();

    await page
      .locator(".address-editor--body")
      .getByRole("textbox", { name: /Straße/ })
      .fill("Beuthener Str. 24");

    await delay(1000);

    await page.getByText("Adresse speichern", { exact: true }).click();

    await expect(page.locator(".alert")).toContainText(
      "Der Bestellwert oder die Adresse hat sich geändert."
    );
  });
};

export const checkAmountInvalidation = async (page) => {
  await test.step("Check if an amount change invalidates payment", async () => {
    await expect(
      page.locator(".steps--container").getByText("Prüfen und Bestellen")
    ).toBeVisible();

    await page.locator('[name="sQuantity"]').first().selectOption("2");
    await page.waitForResponse(
      /checkout\/changeQuantity\/sTargetAction\/confirm/
    );

    await expect(page.locator(".alert")).toContainText(
      "Der Bestellwert oder die Adresse hat sich geändert."
    );
  });
};
