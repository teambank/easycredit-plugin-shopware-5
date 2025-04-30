import { createApiClient, apiDefaultHeaders } from "./api"

async function globalSetup() {
  try {
    console.log("[prepareData] preparing test data in store");

    const apiClient = await createApiClient();

    // Create category
    let response = await apiClient.post("/api/categories", {
      headers: apiDefaultHeaders,
      data: {
        name: "Default",
        active: true,
        parentId: 3,
        description: "Description of new category",
        metaTitle: "Meta Title for New Category",
        metaDescription: "Meta Description for New Category",
        metaKeywords: "keyword1, keyword2",
        category: {},
      },
    });

    if (!response.ok) {
      throw new Error(`Failed to create category: ${response.status} ${response.statusText}`);
    }

    const categoryData = await response.json();
    if (!categoryData?.data?.id) {
      throw new Error("Invalid category response: missing ID");
    }

    const categoryId = categoryData.data.id;
    console.log(`[prepareData] added category with id ${categoryId}`);

    const baseProductData = {
      description: "Description of product",
      active: true,
      taxId: 1,
      categories: [{ id: categoryId }],
      supplier: "Test Company",
    };

    const productsData = [
      {
        name: "Regular Product",
        mainDetail: {
          number: "regular",
          active: true,
          inStock: 9999,
          prices: [{ customerGroupKey: "EK", price: 201 }],
        },
      },
      {
        name: "Below 50",
        mainDetail: {
          number: "below50",
          active: true,
          inStock: 9999,
          prices: [{ customerGroupKey: "EK", price: 5 }],
        },
      },
      {
        name: "Below 200",
        mainDetail: {
          number: "below200",
          active: true,
          inStock: 9999,
          prices: [{ customerGroupKey: "EK", price: 199 }],
        },
      },
      {
        name: "Above 5000",
        mainDetail: {
          number: "above5000",
          active: true,
          inStock: 9999,
          prices: [{ customerGroupKey: "EK", price: 6000 }],
        },
      },
      {
        name: "Above 10000",
        mainDetail: {
          number: "above10000",
          active: true,
          inStock: 9999,
          prices: [{ customerGroupKey: "EK", price: 11000 }],
        },
      },
    ];

    // Create products
    for (const productData of productsData) {
      try {
        const response = await apiClient.post("/api/articles", {
          headers: apiDefaultHeaders,
          data: {
            ...baseProductData,
            ...productData,
          },
        });

        if (!response.ok) {
          throw new Error(`Failed to create product ${productData.mainDetail.number}: ${response.status} ${response.statusText}`);
        }

        const data = await response.text();
        console.log(data);
        console.log(`[prepareData] added product ${productData.mainDetail.number}`);
      } catch (error) {
        console.error(`Error creating product ${productData.mainDetail.number}:`, error);
        throw error; // Re-throw to fail the test setup
      }
    }
  } catch (error) {
    console.error("Error in global setup:", error);
    throw error; // Re-throw to fail the test setup
  }
}

export default globalSetup;
