const createFormData = (params) => {
  const formData = new FormData();
  Object.entries(params).map(([key, value]) => formData.append(key, value));
  return formData;
};

export default createFormData;
