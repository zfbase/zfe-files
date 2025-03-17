import type { Meta, StoryObj } from '@storybook/react';
import { Image } from './Image';
import type { FileImageItem } from '../ImageTypes';

const meta: Meta<typeof Image> = {
  component: Image,
  tags: ['autodocs'],
};

export default meta;
type Story = StoryObj<typeof Image>;

export const Primary: Story = {
  args: {
    item: {
      key: '0',

      previewUrl:
        'http://orpheus.archive.systems:8080/YPkAqEONGAR5AFhtp4wf6XjwUE3pzFu0FwRF_KkhW5c/resize:fill:960:540:1:0/bG9jYWw6LzU2NS8wMC82YWViNWM0My5qcGVn.jpg',
      canvasUrl:
        'https://img.orpheus.ru/OdjR_CdLsPn0-6lBHCZps4ebQBmCzAhh3gIy_IEBovY//bG9jYWw6L2kvNTY1LzAwLzZhZWI1YzQzLmpwZWc.jpg',
      downloadUrl: '/files-image/download/id/56500',

      previewLocal: '',

      deleted: false,
      data: {
        alt: '',
        x: 0,
        y: 0,
        rotate: 0,
      },
      uploadProgress: undefined,
      loading: false,
    } satisfies FileImageItem,

    width: 480,
    height: 270,

    setData: (...data) => alert(JSON.stringify(data)),
  },
};
