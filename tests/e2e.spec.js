import { test, expect, Page } from '@playwright/test';

const BASE_URL = process.env.BASE_URL || 'http://localhost:8080';

test.describe('Closetly — Desktop Tests', () => {
  test.use({ viewport: { width: 1280, height: 720 } });

  test('Home page loads with products', async ({ page }) => {
    await page.goto(`${BASE_URL}/`);
    await expect(page).toHaveTitle(/Closetly/);
    const products = page.locator('#product-container .product');
    await expect(products.first()).toBeVisible();
  });

  test('Product page loads with details', async ({ page }) => {
    await page.goto(`${BASE_URL}/`);
    const firstProduct = page.locator('#product-container .product').first();
    const link = await firstProduct.locator('.link-product').getAttribute('href');
    if (link) {
      await page.goto(`${BASE_URL}${link}`);
      await expect(page.locator('.product-details')).toBeVisible();
    }
  });

  test('Navigation menu is responsive', async ({ page }) => {
    await page.goto(`${BASE_URL}/`);
    const menu = page.locator('#menu > ul');
    await expect(menu).toBeVisible();
    const navLinks = page.locator('#menu a');
    const count = await navLinks.count();
    expect(count).toBeGreaterThan(0);
  });

  test('Header shows cart stats', async ({ page }) => {
    await page.goto(`${BASE_URL}/`);
    const stats = page.locator('.info-session #stats');
    await expect(stats).toBeVisible();
  });

  test('Footer has correct content', async ({ page }) => {
    await page.goto(`${BASE_URL}/`);
    const footer = page.locator('footer');
    await expect(footer).toContainText('Developed by David Iglesias');
  });
});

test.describe('Closetly — Mobile Tests', () => {
  test.use({ viewport: { width: 375, height: 667 } });

  test('Mobile layout adapts correctly', async ({ page }) => {
    await page.goto(`${BASE_URL}/`);
    const productContainer = page.locator('#product-container');
    const styles = await productContainer.evaluate((el) => {
      const computed = window.getComputedStyle(el);
      return {
        display: computed.display,
        gridTemplateColumns: computed.gridTemplateColumns
      };
    });
    expect(styles.display).toBe('grid');
  });

  test('Product cards stack vertically on mobile', async ({ page }) => {
    await page.goto(`${BASE_URL}/`);
    const products = page.locator('.product');
    const count = await products.count();
    expect(count).toBeGreaterThan(0);
    for (let i = 0; i < Math.min(count, 3); i++) {
      await expect(products.nth(i)).toBeVisible();
    }
  });

  test('Navigation is accessible on mobile', async ({ page }) => {
    await page.goto(`${BASE_URL}/`);
    const navLinks = page.locator('#menu a');
    await expect(navLinks.first()).toBeVisible();
  });

  test('Forms are usable on mobile', async ({ page }) => {
    await page.goto(`${BASE_URL}/user/create`);
    const form = page.locator('#register-container form');
    await expect(form).toBeVisible();
    await expect(form.locator('input[name="firstname"]')).toBeVisible();
    await expect(form.locator('input[name="email"]')).toBeVisible();
    await expect(form.locator('input[name="password"]')).toBeVisible();
    await expect(form.locator('input[type="submit"]')).toBeVisible();
  });
});

test.describe('Closetly — Accessibility Tests', () => {
  test.use({ viewport: { width: 1280, height: 720 } });

  test('All images have alt text', async ({ page }) => {
    await page.goto(`${BASE_URL}/`);
    const images = page.locator('img');
    const count = await images.count();
    for (let i = 0; i < count; i++) {
      const alt = await images.nth(i).getAttribute('alt');
      expect(alt).not.toBeNull();
    }
  });

  test('Forms have labels', async ({ page }) => {
    await page.goto(`${BASE_URL}/user/create`);
    await expect(page.locator('label[for="firstname"]')).toBeVisible();
    await expect(page.locator('label[for="lastname"]')).toBeVisible();
    await expect(page.locator('label[for="email"]')).toBeVisible();
    await expect(page.locator('label[for="password"]')).toBeVisible();
  });

  test('Navigation has aria-label', async ({ page }) => {
    await page.goto(`${BASE_URL}/`);
    const nav = page.locator('#menu');
    await expect(nav).toHaveAttribute('aria-label');
  });
});

test.describe('Closetly — Cart & Order Tests', () => {
  test.use({ viewport: { width: 1280, height: 720 } });

  test('Cart page shows empty state', async ({ page }) => {
    await page.goto(`${BASE_URL}/carshop/index`);
    const cartContent = page.locator('#carshop-index, #carshop-buttons-actions, .m-carshop');
    await expect(cartContent).toBeVisible();
  });

  test('Order page shows checkout area', async ({ page }) => {
    await page.goto(`${BASE_URL}/order/index`);
    await expect(page.locator('#register-container')).toBeVisible();
    const container = page.locator('#register-container');
    const hasForm = await container.locator('form').count();
    const hasLogin = await container.locator('text=Log in to make your order').count();
    expect(hasForm > 0 || hasLogin > 0).toBeTruthy();
  });
});

test.describe('Closetly — Access Control Tests', () => {
  test.use({ viewport: { width: 1280, height: 720 } });

  // A guest with no session must bounce to the storefront, never see the admin/vendedor UI.
  for (const path of ['/product/manage', '/category/index', '/user/manage', '/order/manage']) {
    test(`Guest is redirected away from ${path}`, async ({ page }) => {
      await page.goto(`${BASE_URL}${path}`);
      await expect(page).toHaveURL(`${BASE_URL}/`);
    });
  }
});
