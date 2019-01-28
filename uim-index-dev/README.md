# uim-index-dev
## 如果没安装过nodejs,在机器上安装nodejs
```
debian/ubuntu
github地址 https://github.com/nodesource/distributions/blob/master/README.md

curl -sL https://deb.nodesource.com/setup_10.x | bash -
apt-get install -y nodejs

查看是否安装以及版本
node -v 
npm -v
centos自己想办法
```
### 以下命令需在本目录运行而不是网站根目录

## Project setup
```
如果你是新下载的网站程序并且刚装完nodejs,那么运行以下命令

npm install

并且每次更新index时都需要先运行一次这个命令
```

### Compiles and hot-reloads for development
```
npm run serve
```

### Compiles and minifies for production
```
npm run build
此命令构建index页面，会在public/vuedist目录生成对应的index.html、css、js文件

将index.html改名为 index.tpl，移动到resourse/views/material 目录下：
cp -u ../public/vuedist/index.html ../resources/views/material/index.tpl

当前目录下一键运行以上两个命令脚本
./createIndexTpl.sh
首次运行需要获取权限
chmod +x *.sh
```

### Run your tests
```
npm run test
```

### Lints and fixes files
```
npm run lint
```

### Customize configuration
See [Configuration Reference](https://cli.vuejs.org/config/).

### 如果需要开发插件
```
main.js 为入口文件

router.js 路由管理

store.js 状态管理主文件

App.vue 根容器

各个文件夹内容：

views 页面文件，请把定义好的路由相对应的路由组件放在这里

stores 子状态管理，请把store.js中定义的modules放在这里

mixins 组件间共用的属性文件（data/methods/生命周期函数等）请放在这里

js 项目中引用的js静态资源放在这里

directives 自定义指令目录

css 项目中引用的css静态资源放在这里

components 组件目录，包括页面中某个部分的组件和全局组件

assets 存放图片等其他资源
```