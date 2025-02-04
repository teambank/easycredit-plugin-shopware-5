import { createApiClient, apiDefaultHeaders } from "./api"

async function globalSetup() {
  console.log("[prepareData] preparing test data in store");

  const apiClient = await createApiClient();

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
  var categoryData = await response.json();
  let categoryId = categoryData.data.id;
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

  for (const productData of productsData) {
    const response = await apiClient.post("/api/articles", {
      headers: apiDefaultHeaders,
      data: {
          ...baseProductData,
          ...productData,
      },
    });
    const data = await response.text();
    console.log(data);
    console.log(`[prepareData] added product ${productData.mainDetail.number}`);
  }
}

export default globalSetup;
