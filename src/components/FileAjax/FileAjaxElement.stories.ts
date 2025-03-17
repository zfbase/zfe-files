import type { Meta, StoryObj } from '@storybook/react';
import { FileAjaxElement } from './FileAjaxElement';
import { getFileProps, getRootProps } from './getFileAjaxProps';

const meta: Meta<typeof FileAjaxElement> = {
  component: FileAjaxElement,
  tags: ['autodocs'],
};

export default meta;
type Story = StoryObj<typeof FileAjaxElement>;

const rootAttributes: { name: string; value: string }[] = [
  { name: 'data-width', value: '480' },
  { name: 'data-height', value: '270' },
  { name: '0', value: 'Минимальный размер (ш×в): 960×540px' },
  { name: 'dimensionLabel', value: 'sm-2 col-md-12 md-left' },
  { name: 'dimensionControls', value: 'sm-10 col-md-12' },
  { name: 'data-name', value: 'cover' },
  { name: 'class', value: 'zfe-files-ajax' },
  { name: 'data-accept', value: 'image/*' },
  { name: 'data-model-name', value: 'Videos' },
  { name: 'data-schema-code', value: 'cover' },
  { name: 'data-type', value: 'image' },
  { name: 'data-upload-url', value: '/files-image/upload' },
];

const fileDataset: Record<string, string> = {
  name: 'photo-1465847899084-d164df4dedc6.jpeg',
  downloadUrl: '/files-image/download/id/56500',
  hash: '6aeb5c43',
  ext: 'jpeg',
  dataAlt: '',
  dataX: '0',
  dataY: '0',
  dataRotate: '0',
  mediatorId: '108436',
  previewUrl:
    'http://orpheus.archive.systems:8080/YPkAqEONGAR5AFhtp4wf6XjwUE3pzFu0FwRF_KkhW5c/resize:fill:960:540:1:0/bG9jYWw6LzU2NS8wMC82YWViNWM0My5qcGVn.jpg',
  canvasUrl:
    'https://img.orpheus.ru/OdjR_CdLsPn0-6lBHCZps4ebQBmCzAhh3gIy_IEBovY//bG9jYWw6L2kvNTY1LzAwLzZhZWI1YzQzLmpwZWc.jpg',
};

const props = getRootProps(rootAttributes);

export const Primary: Story = {
  args: {
    ...(props as any),
    files: [{ id: '56500', ...getFileProps(fileDataset) }],
  },
};
