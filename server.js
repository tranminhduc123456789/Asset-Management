const jsonServer = require('json-server');
const low = require('lowdb');
const FileSync = require('lowdb/adapters/FileSync');
const Memory = require('lowdb/adapters/Memory');
const fs = require('fs');
const path = require('path');

const app = jsonServer.create();
const dbFilePath = path.join(__dirname, 'db.json');
const adapter = process.env.NODE_ENV === 'production' ? new Memory() : new FileSync(dbFilePath);
const db = low(adapter);

if (process.env.NODE_ENV === 'production') {
  const dbData = JSON.parse(fs.readFileSync(dbFilePath, 'utf-8'));
  db.defaults(dbData).write();
}

const middlewares = jsonServer.defaults();
app.use(middlewares);
app.use(jsonServer.bodyParser);

app.use((req, res, next) => {
  if (req.method !== 'GET') {
    const apiKey = req.headers['x-api-key'];
    const expectedApiKey = '12345678910';

    if (!apiKey || apiKey !== expectedApiKey) {
      return res.status(403).json({ error: 'Forbidden' });
    }
  }
  next();
});

app.post('/chains', (req, res) => {
  const newChain = req.body;
  const chainId = newChain.id;

  const existingChain = db.get('chains').find({ id: chainId }).value();
  if (existingChain) {
    return res.status(400).json({ error: 'Chain with this ID already exists' });
  }

  db.get('chains').push({ id: chainId, name: newChain.name }).write();

  db.get('shortNames').push({ id: chainId, shortName: newChain.shortName, chainId: chainId }).write();
  db.get('chainIds').push({ id: chainId, chainId: chainId, chainIdValue: newChain.chainIdValue }).write();
  db.get('networks').push({ id: chainId, network: newChain.network, chainId: chainId }).write();
  db.get('nativeCurrencies').push({ 
    id: chainId, 
    name: newChain.nativeCurrency.name, 
    symbol: newChain.nativeCurrency.symbol, 
    decimals: newChain.nativeCurrency.decimals, 
    chainId: chainId 
  }).write();
  newChain.rpcUrls.forEach((url, index) => {
    db.get('rpcUrls').push({ id: chainId * 100 + index, url: url, chainId: chainId }).write();
  });
  newChain.blockExplorerUrls.forEach((url, index) => {
    db.get('blockExplorerUrls').push({ id: chainId * 100 + index, url: url, chainId: chainId }).write();
  });

  res.status(201).json(newChain);
});

app.get('/chains/:id/full', (req, res) => {
  const chainId = parseInt(req.params.id, 10);
  const chain = db.get('chains').find({ id: chainId }).value();
  if (!chain) {
    return res.status(404).json({ error: 'not found' });
  }

  const shortName = db.get('shortNames').find({ chainId: chainId }).value();
  const chainIdValue = db.get('chainIds').find({ chainId: chainId }).value();
  const network = db.get('networks').find({ chainId: chainId }).value();
  const nativeCurrency = db.get('nativeCurrencies').find({ chainId: chainId }).value();
  const rpcUrls = db.get('rpcUrls').filter({ chainId: chainId }).map('url').value();
  const blockExplorerUrls = db.get('blockExplorerUrls').filter({ chainId: chainId }).map('url').value();

  const fullChainInfo = {
    ...chain,
    shortName: shortName ? shortName.shortName : null,
    chainIdValue: chainIdValue ? chainIdValue.chainIdValue : null,
    network: network ? network.network : null,
    nativeCurrency: nativeCurrency ? nativeCurrency : null,
    rpcUrls: rpcUrls,
    blockExplorerUrls: blockExplorerUrls
  };

  res.json(fullChainInfo);
});

app.get('/chains/full', (req, res) => {
  const chains = db.get('chains').value();

  const fullChainsInfo = chains.map(chain => {
    const chainId = chain.id;
    const shortName = db.get('shortNames').find({ chainId: chainId }).value();
    const chainIdValue = db.get('chainIds').find({ chainId: chainId }).value();
    const network = db.get('networks').find({ chainId: chainId }).value();
    const nativeCurrency = db.get('nativeCurrencies').find({ chainId: chainId }).value();
    const rpcUrls = db.get('rpcUrls').filter({ chainId: chainId }).map('url').value();
    const blockExplorerUrls = db.get('blockExplorerUrls').filter({ chainId: chainId }).map('url').value();

    return {
      ...chain,
      shortName: shortName ? shortName.shortName : null,
      chainIdValue: chainIdValue ? chainIdValue.chainIdValue : null,
      network: network ? network.network : null,
      nativeCurrency: nativeCurrency ? nativeCurrency : null,
      rpcUrls: rpcUrls,
      blockExplorerUrls: blockExplorerUrls
    };
  });

  res.json(fullChainsInfo);
});

app.get('/chains/:id', (req, res) => {
  const chainId = parseInt(req.params.id, 10);
  const chain = db.get('chains').find({ id: chainId }).value();
  if (!chain) {
    return res.status(404).json({ error: 'not found' });
  }
  res.json(chain);
});

app.use((req, res, next) => {
  req.app.db = db;
  next();
});

const router = jsonServer.router(db);
app.use(router);

const port = process.env.PORT || 3000;
app.listen(port, () => {
  console.log(`JSON Server is running on port ${port}`);
});
