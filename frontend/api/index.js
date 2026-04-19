/**
 * API 请求封装
 */

const BASE_URL = 'http://localhost:8080/api'; // 开发环境，生产环境替换为实际域名

/**
 * 统一请求方法
 */
function request(options) {
  return new Promise((resolve, reject) => {
    // 获取 token
    const token = uni.getStorageSync('token') || '';
    
    uni.request({
      url: BASE_URL + options.url,
      method: options.method || 'GET',
      data: options.data || {},
      header: {
        'Content-Type': 'application/json',
        'Authorization': token ? `Bearer ${token}` : ''
      },
      success: (res) => {
        if (res.statusCode === 200) {
          if (res.data.code === 401) {
            // token 过期，跳转登录
            uni.navigateTo({ url: '/pages/mine/login' });
            reject({ code: 401, msg: '请先登录' });
          } else {
            resolve(res.data);
          }
        } else {
          uni.showToast({ title: '网络错误', icon: 'none' });
          reject(res);
        }
      },
      fail: (err) => {
        uni.showToast({ title: '网络请求失败', icon: 'none' });
        reject(err);
      }
    });
  });
}

// API 模块
const api = {
  // 用户相关
  user: {
    register: (data) => request({ url: '/user/register', method: 'POST', data }),
    login: (data) => request({ url: '/user/login', method: 'POST', data }),
    logout: () => request({ url: '/user/logout', method: 'POST' }),
    info: () => request({ url: '/user/info', method: 'GET' })
  },
  
  // 企业相关
  company: {
    list: () => request({ url: '/company/list', method: 'GET' }),
    detail: (id) => request({ url: '/company/detail', method: 'GET', data: { id } }),
    search: (keyword) => request({ url: '/company/search', method: 'POST', data: { keyword } }),
    collect: (data) => request({ url: '/company/collect', method: 'POST', data }),
    bind: (companyId) => request({ url: '/company/bind', method: 'POST', data: { company_id: companyId } }),
    unbind: (companyId) => request({ url: '/company/unbind', method: 'DELETE', data: { company_id: companyId } })
  },
  
  // 风险分析
  risk: {
    analysis: (companyId) => request({ url: '/risk/analysis', method: 'GET', data: { company_id: companyId } }),
    list: (companyId, params) => request({ url: '/risk/list', method: 'GET', data: { company_id: companyId, ...params } }),
    statistics: (companyId) => request({ url: '/risk/statistics', method: 'GET', data: { company_id: companyId } })
  },
  
  // 报告相关
  report: {
    list: () => request({ url: '/report/list', method: 'GET' }),
    generate: (data) => request({ url: '/report/generate', method: 'POST', data }),
    download: (id) => request({ url: '/report/download', method: 'GET', data: { id } })
  },
  
  // 图表数据
  chart: {
    equity: (companyId) => request({ url: '/chart/equity', method: 'GET', data: { company_id: companyId } }),
    risk: (companyId) => request({ url: '/chart/risk', method: 'GET', data: { company_id: companyId } }),
    qualification: (companyId) => request({ url: '/chart/qualification', method: 'GET', data: { company_id: companyId } })
  }
};

export default api;
