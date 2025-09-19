import { parseUploadResult } from './parseUploadResult';

test('parseUploadResult', () => {
  expect(
    parseUploadResult({
      url: 'http://',
      'data-alt': '',
      dataAlt: '',
      'data-some-test': 'test',
    })
  ).toEqual({
    url: 'http://',
    data: {
      alt: '',
      someTest: 'test',
    },
  });
});
